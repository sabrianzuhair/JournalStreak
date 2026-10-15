<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use App\Support\RichText;
use App\Support\StreakMilestone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Rotating daily writing prompts for the "Mindful Spark" card.
     */
    private const PROMPTS = [
        ['What made you pause and smile today, no matter how small?', 'A fleeting barista smile, sunlight filtering through library blinds, or a solved line of code.'],
        ['What is one thing you learned today that surprised you?', 'A fact from class, something about a friend, or something about yourself.'],
        ['Who made your day a little easier, and how?', 'A classmate who shared notes, a stranger who held the door, a message from home.'],
        ['What drained your energy today, and what restored it?', 'Notice the patterns. Small adjustments start with small observations.'],
        ['If today were a chapter title, what would it be?', 'Sum up the mood of the day in a handful of words, then tell the story behind it.'],
        ['What are you looking forward to this week?', 'Plans, people, or simply a quiet evening with a good book.'],
        ['What would you tell yourself from one year ago?', 'Write it as a short letter. Be kind; you have come a long way.'],
    ];

    /**
     * Cover photos for Recent Stories cards whose entry has no attached image.
     */
    private const STORY_COVERS = [
        'https://lh3.googleusercontent.com/aida-public/AB6AXuDgyc6SWxAmYBFqlNj5WYC2iNHAWmS54P12sgggSbrFuBu8BFRkUj5NxNNobc6PYG6IdQRYDWZ7PojhyUX7fgt8D3hpWUuPWBsOBItE7B0UKZ0O3KaKF0EG4Qv2bvZ4kXaGHkizrGrAg4vQ3X0MpaXA5sKe0NWbVLu1nKb0nkK5jMp1v6IOi4A6iVwvIa4-aJuKInuroWo5BQzC3vJ06SnxHcKROLFSxZM_6W5XgxhVktg9qYVvB0Jy',
        'https://lh3.googleusercontent.com/aida-public/AB6AXuCpLb4aJGm-4Qtrb8r1L_yACdcd1EKcIr4qjZAlQifsWPBzoh1lCxzv3irANJ9-E2m_XXOPj-LH3Rqkw31N829Vs_cieM4aAMOXMMUBJh_K5hsxbc6ULa81inPlwzum42C5vdmWClTXkgKbNd04vwueX_J2i_jrEkscxfkfEUVdRliQDKpFPCYCv9BeeDzFnzKRmcgXiURbg6_0fhpXuQ0jTlbIBBDG5BPynb4ikZj830-dWYmMK85H',
        'https://lh3.googleusercontent.com/aida-public/AB6AXuCpjuKk6RRk8pLPsNhHthitdQP4Hk5bHt5ravHbRsFzn95PxwoLQ0Ll0clvnWw-fXRE_W_4Ff7uOTL6WxPcrFhgakiVQ-xeZWoHfQh7rwuSWJ2E9O_Wx8PsTm2Qq7FnDqfpTMrUHI9PCLIO99MCNyD1pQ78tdf4y7U81QpjAJbPQKyCzb1Vrn5pVhmHW-WZCI7Wm7xSBBsqsVM23wwlb-tz4bcK7mJvyNpttdjAs3gty7IHSPOFWXYa',
    ];

    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $today = today();

        $currentStreak = $user->currentStreak();
        $streakGoal = $user->nextStreakMilestone($currentStreak);
        $longestStreak = $user->longestStreak();

        $published = $user->journalEntries()->published();

        $lastSevenDates = (clone $published)
            ->whereBetween('entry_date', [$today->copy()->subDays(6)->toDateString(), $today->toDateString()])
            ->pluck('entry_date')
            ->map(fn ($date) => $date->toDateString())
            ->flip();

        $lastSevenDays = collect(range(6, 0))->map(function (int $daysAgo) use ($today, $lastSevenDates) {
            $date = $today->copy()->subDays($daysAgo);

            return [
                'date' => $date,
                'written' => $lastSevenDates->has($date->toDateString()),
                'isToday' => $daysAgo === 0,
            ];
        });

        $todayEntry = $user->journalEntries()->whereDate('entry_date', $today)->first();

        $prompt = self::PROMPTS[$today->dayOfYear % count(self::PROMPTS)];

        [$greeting, $greetingIcon] = $this->greeting(now()->hour);
        $totalEntries = (clone $published)->count();

        return view('dashboard', [
            'user' => $user,
            'greeting' => $greeting,
            'greetingIcon' => $greetingIcon,

            // Streak
            'currentStreak' => $currentStreak,
            'longestStreak' => $longestStreak,
            'wroteToday' => $todayEntry !== null && ! $todayEntry->isDraft(),
            'lastSevenDays' => $lastSevenDays,
            'daysToGoal' => $streakGoal - $currentStreak,
            'milestonePercent' => (int) round($currentStreak / $streakGoal * 100),
            'milestoneName' => StreakMilestone::name($streakGoal),
            'milestoneEmoji' => StreakMilestone::emoji($streakGoal),

            // Stats
            'totalEntries' => $totalEntries,
            'totalWords' => (int) (clone $published)->sum('word_count'),
            'entriesThisWeek' => (clone $published)->where('entry_date', '>=', $today->copy()->startOfWeek()->toDateString())->count(),
            'volumeNumber' => intdiv($totalEntries, 30) + 1,

            // Content
            'todayEntry' => $todayEntry,
            'recentEntries' => (clone $published)->with('images')->latest('entry_date')->limit(3)->get(),
            'storyCovers' => self::STORY_COVERS,
            'prompt' => $prompt,
        ]);
    }

    /**
     * Append a quick thought from the dashboard to today's entry, starting a draft if needed.
     */
    public function quickNote(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'note' => ['required', 'string', 'max:2000'],
        ]);

        $entry = $request->user()->journalEntries()->whereDate('entry_date', today())->first()
            ?? $request->user()->journalEntries()->make([
                'entry_date' => today()->toDateString(),
                'status' => JournalEntry::STATUS_DRAFT,
            ]);

        $entry->content = RichText::appendParagraph($entry->content, $validated['note']);
        $entry->save();

        return redirect()->route('dashboard')->with('note_saved', true);
    }

    /**
     * Greeting text and icon for the time of day.
     *
     * @return array{string, string}
     */
    private function greeting(int $hour): array
    {
        return match (true) {
            $hour < 12 => ['Good Morning', 'wb_twilight'],
            $hour < 18 => ['Good Afternoon', 'light_mode'],
            default => ['Good Evening', 'bedtime'],
        };
    }
}
