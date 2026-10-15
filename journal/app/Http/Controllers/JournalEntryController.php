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
