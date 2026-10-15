<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use App\Support\RichText;
use App\Support\StreakMilestone;
use Carbon\CarbonInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProfileController extends Controller
{
    /**
     * Display the profile page (with the "Edit Persona" dialog and password form).
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        return view('profile.edit', [
            'user' => $user,
            ...$this->writingStats($user),
        ]);
    }

    /**
     * Update the user's profile information and portrait.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->safe()->except(['avatar', 'remove_avatar']));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($request->hasFile('avatar') || $request->boolean('remove_avatar')) {
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $user->avatar_path = $request->file('avatar')?->store('avatars', 'public');
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Download every published entry as a single Markdown file.
     */
    public function export(Request $request): StreamedResponse
    {
        $entries = $request->user()->journalEntries()->published()->with('images')->orderBy('entry_date')->get();
        $filename = 'daily-journal-'.today()->toDateString().'.md';

        return response()->streamDownload(function () use ($entries, $request) {
            echo "# {$request->user()->name}'s Daily Journal\n\n";

            foreach ($entries as $entry) {
                echo '## '.($entry->title ?: 'Untitled reflection')."\n\n";
                echo '*'.$entry->entry_date->format('l, F j, Y');
                echo $entry->mood ? ' · '.$entry->mood->label().' '.$entry->mood->emoji() : '';
                echo "*\n\n";
                echo RichText::toMarkdown($entry->content)."\n\n";
                foreach ($entry->images as $image) {
                    echo '![Attached moment]('.$image->url().")\n\n";
                }
                echo "---\n\n";
            }
        }, $filename, ['Content-Type' => 'text/markdown; charset=UTF-8']);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        // Uploaded files are not removed by the database cascade
        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
        }
        Storage::disk('public')->deleteDirectory("entries/{$user->id}");
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Numbers shown in the profile's stat cards.
     *
     * @return array<string, mixed>
     */
    private function writingStats(User $user): array
    {
        $published = $user->journalEntries()->published();
        $today = today();

        $totalEntries = (clone $published)->count();
        $totalWords = (int) (clone $published)->sum('word_count');
        $firstEntryDate = (clone $published)->min('entry_date');
        $daysSinceFirst = $firstEntryDate ? (int) $today->diffInDays($firstEntryDate, true) + 1 : 0;

        $currentStreak = $user->currentStreak();
        $longestRun = $user->longestStreakRun();
        $streakGoal = $user->nextStreakMilestone($currentStreak);
        $writingHour = $this->usualWritingHour((clone $published)->pluck('created_at'));

        return [
            // Total volume
            'totalEntries' => $totalEntries,
            'totalWords' => $totalWords,
            'weeksWriting' => max(1, (int) ceil($daysSinceFirst / 7)),
            'entriesThisWeek' => (clone $published)->where('entry_date', '>=', $today->copy()->startOfWeek()->toDateString())->count(),
            'averageWords' => $totalEntries ? (int) round($totalWords / $totalEntries) : 0,
            'volumeNumeral' => $this->romanNumeral(intdiv($totalEntries, 30) + 1),

            // Current streak
            'currentStreak' => $currentStreak,
            'daysToGoal' => $streakGoal - $currentStreak,
            'milestonePercent' => (int) round($currentStreak / $streakGoal * 100),
            'milestoneName' => StreakMilestone::name($streakGoal),
            'streakGoal' => $streakGoal,

            // Personal best
            'longestRun' => $longestRun,
            'longestRunDates' => $longestRun ? $this->dateRange($longestRun['start'], $longestRun['end']) : null,

            // Rhythm: share of days written since the first entry, and the usual writing hour
            'consistency' => $daysSinceFirst ? round((clone $published)->count() / $daysSinceFirst * 100, 1) : 0,
            'writingWindow' => $writingHour === null ? null
                : today()->setTime($writingHour, 0)->format('g:i A').' – '.today()->setTime($writingHour, 0)->addHour()->format('g:i A'),
            'writingPersona' => $writingHour === null ? null : $this->timeOfDayName($writingHour),

            // Membership
            'daysActive' => (int) $user->created_at->startOfDay()->diffInDays($today, true) + 1,
        ];
    }

    /**
     * The hour of day (0-23) most entries were started in, or null without entries.
     *
     * @param  iterable<Carbon>  $timestamps
     */
    private function usualWritingHour(iterable $timestamps): ?int
    {
        $hours = collect($timestamps)->map(fn ($time) => $time->hour)->countBy();

        return $hours->isEmpty() ? null : $hours->sortDesc()->keys()->first();
    }

    /**
     * "Dawn", "Midday", "Dusk" or "Night" for an hour of the day.
     */
    private function timeOfDayName(int $hour): string
    {
        return match (true) {
            $hour >= 5 && $hour < 11 => 'Dawn',
            $hour >= 11 && $hour < 17 => 'Midday',
            $hour >= 17 && $hour < 23 => 'Dusk',
            default => 'Night',
        };
    }

    /**
     * "Oct 14, 2026", "Jun 12 – Jul 8, 2023" or "Dec 30, 2025 – Jan 2, 2026".
     */
    private function dateRange(CarbonInterface $start, CarbonInterface $end): string
    {
        if ($start->isSameDay($end)) {
            return $start->format('M j, Y');
        }

        $startFormat = $start->isSameYear($end) ? 'M j' : 'M j, Y';

        return $start->format($startFormat).' – '.$end->format('M j, Y');
    }

    private function romanNumeral(int $number): string
    {
        $numerals = ['M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90,
            'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1];
        $result = '';

        foreach ($numerals as $numeral => $value) {
            while ($number >= $value) {
                $result .= $numeral;
                $number -= $value;
            }
        }

        return $result;
    }
}
