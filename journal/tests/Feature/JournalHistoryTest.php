<?php

namespace Tests\Feature;

use App\Enums\Mood;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class JournalHistoryTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-10-15 20:00:00');
        $this->user = User::factory()->create();
    }

    private function entry(string $date, array $attributes = []): JournalEntry
    {
        return JournalEntry::factory()->for($this->user)->on($date)->create($attributes);
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/journal-history')->assertRedirect(route('login'));
    }

    public function test_lists_only_the_users_own_entries_newest_first(): void
    {
        $this->entry('2026-10-01', ['title' => 'First Light']);
        $this->entry('2026-10-14', ['title' => 'Late Bloom']);
        JournalEntry::factory()->on('2026-10-10')->create(['title' => 'Someone Else']);

        $this->actingAs($this->user)->get('/journal-history')
            ->assertOk()
            ->assertSeeInOrder(['Late Bloom', 'First Light'])
            ->assertDontSee('Someone Else');
    }

    public function test_shows_real_stats(): void
    {
        $this->entry('2026-09-20', ['content' => 'one two three']);
        $this->entry('2026-10-02', ['content' => 'four five']);
        $this->entry('2026-10-03', ['content' => 'six'])->update(['status' => JournalEntry::STATUS_DRAFT]);

        $this->actingAs($this->user)->get('/journal-history')
            ->assertViewHas('totalStories', 2)
            ->assertViewHas('totalWords', 5)
            ->assertViewHas('entriesThisMonth', 1)
            ->assertSee('since September 2026');
    }

    public function test_search_matches_title_and_content(): void
    {
        $this->entry('2026-10-01', ['title' => 'Rainy Library', 'content' => 'Books.']);
        $this->entry('2026-10-02', ['title' => 'Walk Home', 'content' => 'A walk in the rain.']);
        $this->entry('2026-10-03', ['title' => 'Sunny', 'content' => 'Beach day.']);

        $this->actingAs($this->user)->get('/journal-history?q=rain')
            ->assertSee('Rainy Library')
            ->assertSee('Walk Home')
            ->assertDontSee('Sunny');
    }

    public function test_mood_filter_and_counts(): void
    {
        $this->entry('2026-10-01', ['title' => 'Glad One', 'mood' => Mood::Happy]);
        $this->entry('2026-10-02', ['title' => 'Glad Two', 'mood' => Mood::Happy]);
        $this->entry('2026-10-03', ['title' => 'Low Day', 'mood' => Mood::Sad]);

        $response = $this->actingAs($this->user)->get('/journal-history?mood=happy')
            ->assertSee('Glad One')
            ->assertDontSee('Low Day');

        // Counts ignore the active mood so every pill stays accurate
        $this->assertSame(2, $response->viewData('moodCounts')['happy']);
        $this->assertSame(1, $response->viewData('moodCounts')['sad']);
        $response->assertViewHas('allCount', 3);
    }

    public function test_month_filter_and_oldest_first_sort(): void
    {
        $this->entry('2026-09-30', ['title' => 'September Story']);
        $this->entry('2026-10-01', ['title' => 'October Start']);
        $this->entry('2026-10-12', ['title' => 'October Middle']);

        $this->actingAs($this->user)->get('/journal-history?month=2026-10&sort=oldest')
            ->assertSeeInOrder(['October Start', 'October Middle'])
            ->assertDontSee('September Story');
    }

    public function test_invalid_filters_are_rejected(): void
    {
        $this->actingAs($this->user)->get('/journal-history?mood=ecstatic&month=october')
            ->assertSessionHasErrors(['mood', 'month']);
    }

    public function test_entries_are_paginated(): void
    {
        foreach (range(1, 10) as $day) {
            $this->entry(sprintf('2026-10-%02d', $day), ['title' => "Day {$day} story"]);
        }

        $this->actingAs($this->user)->get('/journal-history?page=2')
            ->assertSee('Day 1 story')
            ->assertDontSee('Day 10 story')
            ->assertSee('Page 2 of 2');
    }

    public function test_deleting_an_entry_removes_it_and_its_photos(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('entries/photo.png', 'png');
        $entry = $this->entry('2026-10-14');
        $entry->images()->create(['path' => 'entries/photo.png']);

        $this->actingAs($this->user)->from('/journal-history')->delete("/journal-history/{$entry->id}")
            ->assertRedirect('/journal-history')
            ->assertSessionHas('status', 'Entry from Oct 14, 2026 deleted.');

        $this->assertModelMissing($entry);
        $this->assertDatabaseCount('journal_entry_images', 0);
        Storage::disk('public')->assertMissing('entries/photo.png');
    }

    public function test_users_cannot_delete_someone_elses_entry(): void
    {
        $entry = JournalEntry::factory()->on('2026-10-14')->create();

        $this->actingAs($this->user)->delete("/journal-history/{$entry->id}")->assertNotFound();

        $this->assertModelExists($entry);
    }
}
