<?php

namespace Tests\Feature;

use App\Enums\Mood;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CalendarTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // A Thursday; October 2026 starts on a Thursday too
        Carbon::setTestNow('2026-10-15 20:00:00');
        $this->user = User::factory()->create();
    }

    private function entry(string $date, array $attributes = []): JournalEntry
    {
        return JournalEntry::factory()->for($this->user)->on($date)->create($attributes);
    }

    private function day($response, string $date): array
    {
        return collect($response->viewData('days'))->first(fn ($day) => $day['date']->toDateString() === $date);
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/calendar')->assertRedirect(route('login'));
    }

    public function test_shows_the_current_month_with_today_selected(): void
    {
        $response = $this->actingAs($this->user)->get('/calendar')
            ->assertOk()
            ->assertSee('October 2026')
            ->assertSee('A Blank Page');

        // Sunday Sep 27 to Saturday Oct 31: five full weeks
        $this->assertCount(35, $response->viewData('days'));
        $this->assertTrue($this->day($response, '2026-10-15')['isSelected']);
        $this->assertFalse($this->day($response, '2026-09-27')['inMonth']);
    }

    public function test_selected_day_shows_its_entry(): void
    {
        $this->entry('2026-10-03', ['title' => 'Coffee Tasting', 'content' => '<p>Bitter and <b>bright</b>.</p>', 'mood' => Mood::Great]);

        $this->actingAs($this->user)->get('/calendar?month=2026-10&date=2026-10-03')
            ->assertSee('Coffee Tasting')
            ->assertSee('<b>bright</b>', false)
            ->assertSee('Great mood')
            ->assertSee(route('write-journal', ['date' => '2026-10-03']), false);
    }

    public function test_only_the_users_own_entries_appear(): void
    {
        JournalEntry::factory()->on('2026-10-03')->create(['title' => 'Someone Else']);

        $response = $this->actingAs($this->user)->get('/calendar?date=2026-10-03')
            ->assertDontSee('Someone Else');

        $this->assertNull($this->day($response, '2026-10-03')['entry']);
    }

    public function test_current_streak_days_are_numbered(): void
    {
        foreach (['2026-10-10', '2026-10-12', '2026-10-13', '2026-10-14', '2026-10-15'] as $date) {
            $this->entry($date);
        }

        $response = $this->actingAs($this->user)->get('/calendar?date=2026-10-14')
            ->assertSee('4 Consecutive Days')
            ->assertSee('Active Streak (Oct 12–Oct 15)')
            ->assertSee('3rd Streak Day');

        $this->assertSame(1, $this->day($response, '2026-10-12')['streakDay']);
        $this->assertNull($this->day($response, '2026-10-10')['streakDay']);
    }

    public function test_streak_runs_to_yesterday_when_today_is_unwritten(): void
    {
        $this->entry('2026-10-13');
        $this->entry('2026-10-14');
        $this->entry('2026-10-15')->update(['status' => JournalEntry::STATUS_DRAFT]);

        $response = $this->actingAs($this->user)->get('/calendar');

        $this->assertSame(2, $this->day($response, '2026-10-14')['streakDay']);
        $this->assertNull($this->day($response, '2026-10-15')['streakDay']);
    }

    public function test_writing_rate_counts_published_days_so_far(): void
    {
        // 3 published of 15 days so far; drafts do not count
        $this->entry('2026-10-01');
        $this->entry('2026-10-02');
        $this->entry('2026-10-03');
        $this->entry('2026-10-04')->update(['status' => JournalEntry::STATUS_DRAFT]);

        $this->actingAs($this->user)->get('/calendar')
            ->assertViewHas('writingRate', 20)
            ->assertViewHas('publishedThisMonth', 3);
    }

    public function test_past_months_can_be_browsed_but_not_future_ones(): void
    {
        $this->entry('2026-09-05', ['title' => 'September Memory']);

        $this->actingAs($this->user)->get('/calendar?month=2026-09&date=2026-09-05')
            ->assertSee('September 2026')
            ->assertSee('September Memory')
            ->assertViewHas('canGoForward', true);

        $this->actingAs($this->user)->get('/calendar')->assertViewHas('canGoForward', false);
    }

    public function test_future_days_offer_no_write_button(): void
    {
        $this->actingAs($this->user)->get('/calendar?date=2026-10-20')
            ->assertSee('Not Yet Written')
            ->assertDontSee('Write for this Date');
    }

    public function test_favorite_weekday_insight(): void
    {
        // Two Mondays and one Tuesday
        $this->entry('2026-10-05');
        $this->entry('2026-10-12');
        $this->entry('2026-10-13');

        $this->actingAs($this->user)->get('/calendar')->assertSee('You write most often on Mondays.');
    }

    public function test_invalid_month_is_rejected(): void
    {
        $this->actingAs($this->user)->get('/calendar?month=oct')->assertSessionHasErrors('month');
    }
}
