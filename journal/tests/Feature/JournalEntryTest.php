<?php

namespace Tests\Feature;

use App\Enums\Mood;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class JournalEntryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-10-15 20:00:00');
    }

    private function entriesOn(User $user, array $dates, string $status = JournalEntry::STATUS_PUBLISHED): void
    {
        foreach ($dates as $date) {
            JournalEntry::factory()->for($user)->on($date)->create(['status' => $status]);
        }
    }

    public function test_entry_belongs_to_user_and_casts_fields(): void
    {
        $user = User::factory()->create();

        $entry = $user->journalEntries()->create([
            'entry_date' => '2026-10-15',
            'title' => 'Coffee at the Corner Library',
            'content' => 'Finally understood Dijkstra today.',
            'mood' => 'great',
            'tags' => ['CampusLife', 'CSProject'],
        ])->fresh();

        $this->assertTrue($entry->user->is($user));
        $this->assertSame(Mood::Great, $entry->mood);
        $this->assertSame(['CampusLife', 'CSProject'], $entry->tags);
        $this->assertSame('2026-10-15', $entry->entry_date->toDateString());
        $this->assertSame(JournalEntry::STATUS_PUBLISHED, $entry->status);
    }

    public function test_word_count_is_kept_in_sync_with_content(): void
    {
        $entry = JournalEntry::factory()->create(['content' => 'one two three']);
        $this->assertSame(3, $entry->word_count);

        $entry->update(['content' => 'just two']);
        $this->assertSame(2, $entry->fresh()->word_count);
    }

    public function test_user_can_only_have_one_entry_per_day(): void
    {
        $user = User::factory()->create();
        $this->entriesOn($user, ['2026-10-15']);

        $this->expectException(QueryException::class);
        $this->entriesOn($user, ['2026-10-15']);
    }

    public function test_entries_are_deleted_with_their_user(): void
    {
        $user = User::factory()->create();
        $this->entriesOn($user, ['2026-10-14', '2026-10-15']);

        $user->delete();

        $this->assertDatabaseCount('journal_entries', 0);
    }

    public function test_current_streak_counts_back_from_today(): void
    {
        $user = User::factory()->create();
        $this->entriesOn($user, ['2026-10-13', '2026-10-14', '2026-10-15', '2026-10-10']);

        $this->assertSame(3, $user->currentStreak());
    }

    public function test_current_streak_survives_until_today_is_written(): void
    {
        $user = User::factory()->create();
        $this->entriesOn($user, ['2026-10-13', '2026-10-14']);

        $this->assertSame(2, $user->currentStreak());
    }

    public function test_current_streak_resets_after_a_missed_day(): void
    {
        $user = User::factory()->create();
        $this->entriesOn($user, ['2026-10-12', '2026-10-13']);

        $this->assertSame(0, $user->currentStreak());
    }

    public function test_drafts_do_not_count_toward_streaks(): void
    {
        $user = User::factory()->create();
        $this->entriesOn($user, ['2026-10-14']);
        $this->entriesOn($user, ['2026-10-15'], JournalEntry::STATUS_DRAFT);

        $this->assertSame(1, $user->currentStreak());
        $this->assertSame(1, $user->longestStreak());
    }

    public function test_longest_streak_finds_the_best_run(): void
    {
        $user = User::factory()->create();
        $this->entriesOn($user, [
            '2026-09-01', '2026-09-02', '2026-09-03', '2026-09-04',
            '2026-09-10', '2026-09-11',
            '2026-10-15',
        ]);

        $this->assertSame(4, $user->longestStreak());
        $this->assertSame(1, $user->currentStreak());
    }

    public function test_streaks_are_zero_without_entries(): void
    {
        $user = User::factory()->create();

        $this->assertSame(0, $user->currentStreak());
        $this->assertSame(0, $user->longestStreak());
    }
}
