<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Support\StreakMilestone;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'full_name',
        'handle',
        'email',
        'affiliation',
        'bio',
        'avatar_path',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class);
    }

    /**
     * Consecutive days with a published entry, ending today. If today has no
     * entry yet the streak is still alive, so counting starts from yesterday.
     */
    public function currentStreak(?CarbonInterface $today = null): int
    {
        $today = ($today ?? today())->toImmutable()->startOfDay();
        $dates = $this->publishedEntryDates()->flip();

        $day = $dates->has($today->toDateString()) ? $today : $today->subDay();
        $streak = 0;

        while ($dates->has($day->toDateString())) {
            $streak++;
            $day = $day->subDay();
        }

        return $streak;
    }

    /**
     * The longest run of consecutive days with a published entry.
     */
    public function longestStreak(): int
    {
        return $this->longestStreakRun()['days'] ?? 0;
    }

    /**
     * The longest run of consecutive published days, with its first and last date.
     * The earliest run wins a tie. Null when there are no published entries.
     *
     * @return array{days: int, start: CarbonImmutable, end: CarbonImmutable}|null
     */
    public function longestStreakRun(): ?array
    {
        $best = null;
        $runStart = null;
        $previous = null;

        foreach ($this->publishedEntryDates() as $date) {
            $date = CarbonImmutable::parse($date);

            if (! $previous || ! $previous->addDay()->isSameDay($date)) {
                $runStart = $date;
            }

            $days = (int) $runStart->diffInDays($date) + 1;
            if (! $best || $days > $best['days']) {
                $best = ['days' => $days, 'start' => $runStart, 'end' => $date];
            }

            $previous = $date;
        }

        return $best;
    }

    /**
     * The next streak milestone to aim for.
     */
    public function nextStreakMilestone(?int $streak = null): int
    {
        return StreakMilestone::after($streak ?? $this->currentStreak());
    }

    /**
     * URL of the uploaded portrait, or null to show initials instead.
     */
    public function avatarUrl(): ?string
    {
        // asset() follows the current host, so it works on 127.0.0.1:8000 as well as APP_URL
        return $this->avatar_path ? asset('storage/'.$this->avatar_path) : null;
    }

    /**
     * Up to two initials from the pen name, e.g. "Sabrian Zuhair" -> "SZ".
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(fn (string $word) => Str::upper(Str::substr($word, 0, 1)))
            ->implode('');
    }

    /**
     * Distinct published entry dates as Y-m-d strings, oldest first.
     *
     * @return Collection<int, string>
     */
    protected function publishedEntryDates(): Collection
    {
        return $this->journalEntries()
            ->published()
            ->orderBy('entry_date')
            ->pluck('entry_date')
            ->map(fn ($date) => $date->toDateString())
            ->unique()
            ->values();
    }
}
