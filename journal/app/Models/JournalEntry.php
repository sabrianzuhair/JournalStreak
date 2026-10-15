<?php

namespace App\Models;

use App\Enums\Mood;
use App\Support\RichText;
use Database\Factories\JournalEntryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class JournalEntry extends Model
{
    /** @use HasFactory<JournalEntryFactory> */
    use HasFactory;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'entry_date',
        'title',
        'content',
        'mood',
        'tags',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'entry_date' => 'date',
            'mood' => Mood::class,
            'tags' => 'array',
            'word_count' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        // Keep word_count in sync so stats can be summed in the database
        static::saving(function (JournalEntry $entry) {
            $entry->word_count = str_word_count(RichText::toPlainText($entry->content));
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Attached photos ("Image Moments"), oldest first.
     */
    public function images(): HasMany
    {
        return $this->hasMany(JournalEntryImage::class)->oldest('id');
    }

    /**
     * Only finished entries count toward streaks and stats.
     */
    public function scopePublished(Builder $query): void
    {
        $query->where('status', self::STATUS_PUBLISHED);
    }

    public function scopeDrafts(Builder $query): void
    {
        $query->where('status', self::STATUS_DRAFT);
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * Estimated reading time in minutes (about 180 words per minute, minimum 1).
     */
    public function readingMinutes(): int
    {
        return max(1, (int) ceil($this->word_count / 180));
    }

    /**
     * Friendly date such as "Today · Oct 15", "Yesterday · Oct 14" or "Sunday · Oct 12".
     */
    public function dayLabel(): string
    {
        $day = match (true) {
            $this->entry_date->isToday() => 'Today',
            $this->entry_date->isYesterday() => 'Yesterday',
            default => $this->entry_date->format('l'),
        };

        return $day.' · '.$this->entry_date->format('M j');
    }

    public function excerpt(int $limit = 160): string
    {
        return Str::limit(Str::squish(RichText::toPlainText($this->content)), $limit);
    }
}
