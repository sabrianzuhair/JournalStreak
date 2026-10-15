<?php

namespace Tests\Feature;

use App\Enums\Mood;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // A Thursday, so the week started on Monday 2026-10-12
        Carbon::setTestNow('2026-10-15 20:00:00');
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_new_user_sees_an_empty_dashboard(): void
    {
        $user = User::factory()->create(['name' => 'Sabrian']);

        $this->actingAs($user)->get('/dashboard')
            ->assertOk()
            ->assertSee('Sabrian')
            ->assertSee('Start your streak tonight.')
            ->assertSee('Not logged yet')
            ->assertSee('Your first story is waiting')
            ->assertSee('7 days until', false)
            ->assertDontSee('Coffee at the Corner Library')
            ->assertDontSee('42,650');
    }

    public function test_dashboard_shows_real_streak_and_stats(): void
    {
        $user = User::factory()->create();

        // Current run: 13th-15th (3 days). Earlier best run: 1st-5th (5 days).
        foreach (['2026-10-01', '2026-10-02', '2026-10-03', '2026-10-04', '2026-10-05', '2026-10-13', '2026-10-14'] as $date) {
            JournalEntry::factory()->for($user)->on($date)->create(['content' => 'one two three four five']);
        }
        JournalEntry::factory()->for($user)->on('2026-10-15')->create([
            'title' => 'Tonight by the window',
            'content' => 'one two three four five',
            'mood' => Mood::Happy,
        ]);
        JournalEntry::factory()->for($user)->on('2026-10-10')->draft()->create(['title' => 'Unfinished draft']);

        $this->actingAs($user)->get('/dashboard')
            ->assertOk()
            ->assertViewHas('currentStreak', 3)
            ->assertViewHas('longestStreak', 5)
            ->assertViewHas('totalEntries', 8)
            ->assertViewHas('totalWords', 40)
            ->assertViewHas('entriesThisWeek', 3)
            ->assertViewHas('wroteToday', true)
            ->assertSee('Today\'s page is written.', false)
            ->assertSee('Happy')
            ->assertSee('“Tonight by the window”', false)
            ->assertSee('4 days until', false)
            ->assertSee('1-Week Milestone Badge', false)
            ->assertSee('43% achieved')
            ->assertDontSee('Unfinished draft');
    }

    public function test_last_seven_days_marks_written_days(): void
    {
        $user = User::factory()->create();
        JournalEntry::factory()->for($user)->on('2026-10-14')->create();
        JournalEntry::factory()->for($user)->on('2026-10-09')->create();

        $days = $this->actingAs($user)->get('/dashboard')->viewData('lastSevenDays');

        $this->assertSame('2026-10-09', $days->first()['date']->toDateString());
        $this->assertSame('2026-10-15', $days->last()['date']->toDateString());
        $this->assertSame(
            [true, false, false, false, false, true, false],
            $days->pluck('written')->all(),
        );
        $this->assertTrue($days->last()['isToday']);
    }

    public function test_recent_stories_show_latest_three_published_entries(): void
    {
        $user = User::factory()->create();
        foreach (['2026-10-11' => 'Oldest', '2026-10-12' => 'Third', '2026-10-13' => 'Second', '2026-10-14' => 'Newest'] as $date => $title) {
            JournalEntry::factory()->for($user)->on($date)->create(['title' => $title]);
        }

        $this->actingAs($user)->get('/dashboard')
            ->assertSeeInOrder(['Newest', 'Second', 'Third'])
            ->assertDontSee('Oldest')
            ->assertSee('Yesterday · Oct 14', false)
            ->assertSee(route('write-journal', ['date' => '2026-10-14']), false);
    }

    public function test_quick_note_starts_a_draft_for_today(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/dashboard/quick-note', ['note' => 'Sunlight through the library blinds.'])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('note_saved');

        $entry = $user->journalEntries()->sole();
        $this->assertTrue($entry->isDraft());
        $this->assertSame('2026-10-15', $entry->entry_date->toDateString());
        $this->assertSame('<p>Sunlight through the library blinds.</p>', $entry->content);
        $this->assertSame(0, $user->currentStreak());
    }

    public function test_quick_note_appends_to_todays_entry(): void
    {
        $user = User::factory()->create();
        JournalEntry::factory()->for($user)->on('2026-10-15')->create(['content' => 'Morning thoughts.']);

        $this->actingAs($user)->post('/dashboard/quick-note', ['note' => 'Evening addition.']);

        $entry = $user->journalEntries()->sole();
        $this->assertSame('<p>Morning thoughts.</p><p>Evening addition.</p>', $entry->content);
        $this->assertSame(4, $entry->word_count);
        $this->assertFalse($entry->isDraft());
    }

    public function test_quick_note_requires_text(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/dashboard/quick-note', ['note' => ''])
            ->assertSessionHasErrors('note');

        $this->assertSame(0, $user->journalEntries()->count());
    }
}
