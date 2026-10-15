<?php

namespace App\Http\Controllers;

use App\Support\StreakMilestone;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class CalendarController extends Controller
{
    /**
     * A month grid of entries with the selected day's entry beside it.
     */
    public function __invoke(Request $request): View
    {
        $validated = $request->validate([
            'month' => ['nullable', 'date_format:Y-m'],
            'date' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $user = $request->user();
        $today = today();

        $month = isset($validated['month'])
            ? Carbon::createFromFormat('Y-m', $validated['month'])->startOfMonth()
            : $today->copy()->startOfMonth();
        $monthEnd = $month->copy()->endOfMonth();

        // The selected day stays inside the shown month: today if visible, otherwise the 1st
        $selected = isset($validated['date']) ? Carbon::parse($validated['date']) : $today->copy();
        if (! $selected->isSameMonth($month)) {
            $selected = $month->copy();
        }

        $entries = $user->journalEntries()->with('images')
            ->whereBetween('entry_date', [$month->toDateString(), $monthEnd->toDateString()])
            ->get()
            ->keyBy(fn ($entry) => $entry->entry_date->toDateString());

        // The current streak ends today, or yesterday if today is not written yet
        $currentStreak = $user->currentStreak();
        $wroteToday = $user->journalEntries()->published()->whereDate('entry_date', $today)->exists();
        $streakEnd = $wroteToday ? $today->copy() : $today->copy()->subDay();
        $streakStart = $streakEnd->copy()->subDays(max($currentStreak - 1, 0));

        $days = [];
        $cursor = $month->copy()->startOfWeek(CarbonInterface::SUNDAY);
        $gridEnd = $monthEnd->copy()->endOfWeek(CarbonInterface::SATURDAY);
        for (; $cursor->lte($gridEnd); $cursor->addDay()) {
            $inStreak = $currentStreak > 0 && $cursor->between($streakStart, $streakEnd);
            $days[] = [
                'date' => $cursor->copy(),
                'inMonth' => $cursor->isSameMonth($month),
                'entry' => $entries->get($cursor->toDateString()),
                'streakDay' => $inStreak ? (int) $streakStart->diffInDays($cursor) + 1 : null,
                'isToday' => $cursor->isSameDay($today),
                'isFuture' => $cursor->gt($today),
                'isSelected' => $cursor->isSameDay($selected),
            ];
        }

        $publishedThisMonth = $entries->filter(fn ($entry) => ! $entry->isDraft())->count();
        // Days that could have been written so far: the whole month in the past, up to today in this one
        $elapsedDays = $month->gt($today) ? 0 : ($month->isSameMonth($today) ? $today->day : $month->daysInMonth);
        $streakGoal = $user->nextStreakMilestone($currentStreak);

        $favoriteWeekday = $user->journalEntries()->published()->pluck('entry_date')
            ->countBy(fn ($date) => $date->format('l'))
            ->sortDesc()
            ->keys()
            ->first();

        $selectedEntry = $entries->get($selected->toDateString());

        return view('journal.calendar', [
            'month' => $month,
            'previousMonth' => $month->copy()->subMonth(),
            'nextMonth' => $month->copy()->addMonth(),
            'canGoForward' => $month->lt($today->copy()->startOfMonth()),
            'days' => $days,
            'selected' => $selected,
            'selectedEntry' => $selectedEntry,
            'selectedStreakDay' => collect($days)->firstWhere('isSelected')['streakDay'] ?? null,
            'currentStreak' => $currentStreak,
            'streakRange' => $currentStreak > 0 ? [$streakStart, $streakEnd] : null,
            'milestoneName' => StreakMilestone::name($streakGoal),
            'publishedThisMonth' => $publishedThisMonth,
            'writingRate' => $elapsedDays ? (int) round($publishedThisMonth / $elapsedDays * 100) : null,
            'favoriteWeekday' => $favoriteWeekday,
        ]);
    }
}
