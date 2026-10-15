<?php

namespace App\Http\Controllers;

use App\Enums\Mood;
use App\Models\JournalEntry;
use App\Support\RichText;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class JournalEntryController extends Controller
{
    /**
     * Most photos one entry can hold.
     */
    public const MAX_IMAGES = 6;

    /**
     * Entries per page on the history archive.
     */
    public const PER_PAGE = 9;

    /**
     * The archive of all entries, with search, mood and month filters.
     */
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'mood' => ['nullable', Rule::enum(Mood::class)],
            'month' => ['nullable', 'date_format:Y-m'],
            'sort' => ['nullable', Rule::in(['newest', 'oldest'])],
        ]);

        $user = $request->user();
        $entries = $user->journalEntries();

        if ($search = $validated['q'] ?? null) {
            // shortcut: matches raw HTML too, so searching "strong" finds bold text; strip tags on save if it matters
            $entries->where(fn ($query) => $query
                ->where('title', 'like', "%{$search}%")
                ->orWhere('content', 'like', "%{$search}%"));
        }

        if ($month = $validated['month'] ?? null) {
            $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
            $entries->whereBetween('entry_date', [$start->toDateString(), $start->copy()->endOfMonth()->toDateString()]);
        }

        // Pill counts follow the search and month filters but not the mood itself
        $moodCounts = (clone $entries)->whereNotNull('mood')->toBase()
            ->selectRaw('mood, count(*) as total')->groupBy('mood')->pluck('total', 'mood');
        $allCount = (clone $entries)->count();

        if ($mood = $validated['mood'] ?? null) {
            $entries->where('mood', $mood);
        }

        $published = $user->journalEntries()->published();
        $firstDate = (clone $published)->min('entry_date');

        return view('journal.history', [
            'entries' => $entries
                ->orderBy('entry_date', ($validated['sort'] ?? 'newest') === 'oldest' ? 'asc' : 'desc')
                ->paginate(self::PER_PAGE)
                ->withQueryString(),
            'filters' => $validated,
            'moods' => Mood::cases(),
            'moodCounts' => $moodCounts,
            'allCount' => $allCount,
            'months' => $user->journalEntries()->orderByDesc('entry_date')->pluck('entry_date')
                ->map(fn ($date) => $date->format('Y-m'))->unique()->values(),
            'totalStories' => (clone $published)->count(),
            'firstDate' => $firstDate ? Carbon::parse($firstDate) : null,
            'totalWords' => (int) (clone $published)->sum('word_count'),
            'entriesThisMonth' => (clone $published)->where('entry_date', '>=', today()->startOfMonth()->toDateString())->count(),
        ]);
    }

    /**
     * Delete one of the user's entries together with its photos.
     */
    public function destroy(Request $request, int $entry): RedirectResponse
    {
        $entry = $request->user()->journalEntries()->findOrFail($entry);

        // Delete photos through the model so their files are removed too
        $entry->images->each->delete();
        $entry->delete();

        return back()->with('status', 'Entry from '.$entry->entry_date->format('M j, Y').' deleted.');
    }

    /**
     * Show the editor for a day (today by default), prefilled if that day already has an entry.
     */
    public function create(Request $request): View
    {
        $validated = $request->validate([
            'date' => ['nullable', 'date_format:Y-m-d', 'before_or_equal:today'],
        ]);

        $user = $request->user();
        $date = Carbon::parse($validated['date'] ?? today());

        $entry = $user->journalEntries()->whereDate('entry_date', $date)->first()
            ?? new JournalEntry(['entry_date' => $date, 'status' => JournalEntry::STATUS_DRAFT]);

        return view('journal.write', [
            'entry' => $entry,
            'date' => $date,
            'currentStreak' => $user->currentStreak(),
            'moods' => Mood::cases(),
            // Values the user just submitted win over the saved ones (e.g. after a validation error)
            'selectedMood' => Mood::tryFrom((string) old('mood', $entry->mood?->value)),
            'editorHtml' => RichText::toEditorHtml(old('content', $entry->content)),
            'maxImages' => self::MAX_IMAGES,
        ]);
    }

    /**
     * Save the entry for its day, creating it or updating the existing one.
     */
    public function store(Request $request): RedirectResponse
    {
        // The editor sends HTML: clean it first so "empty" formatting counts as no content
        $request->merge(['content' => RichText::sanitize($request->input('content'))]);

        $validated = $request->validate([
            'entry_date' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string', 'max:100000', 'required_if:action,publish'],
            'mood' => ['nullable', Rule::enum(Mood::class)],
            'images' => ['array', 'max:'.self::MAX_IMAGES],
            'images.*' => ['image', 'max:5120'],
            'remove_images' => ['array'],
            'remove_images.*' => ['integer'],
            'action' => ['required', Rule::in(['draft', 'publish'])],
        ], [
            'content.required_if' => 'Write a few words before saving your entry.',
            'images.max' => 'You can attach up to '.self::MAX_IMAGES.' images to an entry.',
            'images.*.image' => 'Attachments must be images (JPG, PNG, GIF or WebP).',
            'images.*.max' => 'Each image must be 5 MB or smaller.',
        ]);

        $user = $request->user();
        $date = $validated['entry_date'];

        $entry = $user->journalEntries()->whereDate('entry_date', $date)->first()
            ?? $user->journalEntries()->make(['entry_date' => $date]);

        $removeIds = $validated['remove_images'] ?? [];
        $newImages = $request->file('images', []);
        $keptCount = $entry->exists ? $entry->images()->whereNotIn('id', $removeIds)->count() : 0;

        if ($keptCount + count($newImages) > self::MAX_IMAGES) {
            return back()->withInput()->withErrors([
                'images' => 'You can attach up to '.self::MAX_IMAGES.' images to an entry.',
            ]);
        }

        // Saving a draft never un-publishes an entry that already counts toward the streak
        $status = $validated['action'] === 'publish' || ($entry->exists && ! $entry->isDraft())
            ? JournalEntry::STATUS_PUBLISHED
            : JournalEntry::STATUS_DRAFT;

        $entry->fill([
            'title' => $validated['title'] ?? null,
            'content' => $validated['content'] ?? null,
            'mood' => $validated['mood'] ?? null,
            'status' => $status,
        ])->save();

        // Only images that belong to this entry can be removed; deleting the record deletes the file
        $entry->images()->whereIn('id', $removeIds)->get()->each->delete();

        foreach ($newImages as $file) {
            $entry->images()->create(['path' => $file->store("entries/{$user->id}", 'public')]);
        }

        $message = $status === JournalEntry::STATUS_PUBLISHED
            ? "Journal entry saved to your vault. Day {$user->currentStreak()} streak secured!"
            : 'Draft saved. Publish it to secure your streak.';

        return redirect()
            ->route('write-journal', $entry->entry_date->isToday() ? [] : ['date' => $date])
            ->with('status', $message);
    }
}
