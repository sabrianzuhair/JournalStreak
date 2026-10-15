<?php

namespace App\Support;

/**
 * Streak milestones (7 days, 2 weeks, 1 month, ...) and their display names.
 */
class StreakMilestone
{
    /**
     * Milestone length in days => [name, emoji].
     */
    private const MILESTONES = [
        7 => ['1-Week', '🌱'],
        14 => ['2-Week', '🌱'],
        30 => ['1-Month', '🌿'],
        60 => ['2-Month', '🌳'],
        100 => ['100-Day', '🏆'],
        365 => ['1-Year', '🏆'],
    ];

    /**
     * The next milestone to aim for; after a year, every further 365 days.
     */
    public static function after(int $streak): int
    {
        foreach (array_keys(self::MILESTONES) as $days) {
            if ($streak < $days) {
                return $days;
            }
        }

        return (intdiv($streak, 365) + 1) * 365;
    }

    public static function name(int $days): string
    {
        return self::MILESTONES[$days][0] ?? ($days / 365).'-Year';
    }

    public static function emoji(int $days): string
    {
        return self::MILESTONES[$days][1] ?? '🏆';
    }
}
