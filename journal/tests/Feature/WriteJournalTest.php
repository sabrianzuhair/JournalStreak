<?php

namespace Tests\Feature;

use App\Enums\Mood;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class WriteJournalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-10-15 20:00:00');
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'entry_date' => '2026-10-15',
            'title' => 'The Quiet Magic of Thursday Evenings',
            'content' => 'Today felt noticeably lighter.',
            'mood' => 'great',
            'action' => 'publish',
        ], $overrides);
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/write-journal')->assertRedirect(route('login'));
        $this->post('/write-journal', $this->payload())->assertRedirect(route('login'));
    }

    public function test_write_page_renders_blank_for_a_new_day(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/write-journal')
            ->assertOk()
            ->assertSee('New entry')
            ->assertSee('Day 1 streak will be secured upon saving')
            ->assertDontSee('The Quiet Magic of Tuesday Evenings');
    }

    public function test_write_page_loads_the_existing_entry_for_the_day(): void
    {
        $user = User::factory()->create();
        JournalEntry::factory()->for($user)->on('2026-10-15')->create(['title' => 'Already written today']);

        $this->actingAs($user)->get('/write-journal')
            ->assertOk()
            ->assertSee('Already written today')
            ->assertSee('Day 1 streak secured');
    }

    public function test_publishing_creates_an_entry_and_secures_the_streak(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/write-journal', $this->payload())
            ->assertRedirect(route('write-journal'))
            ->assertSessionHas('status', 'Journal entry saved to your vault. Day 1 streak secured!');

        $entry = $user->journalEntries()->sole();
        $this->assertSame('The Quiet Magic of Thursday Evenings', $entry->title);
        $this->assertSame(Mood::Great, $entry->mood);
        $this->assertSame(JournalEntry::STATUS_PUBLISHED, $entry->status);
        $this->assertSame(4, $entry->word_count);
        $this->assertSame(1, $user->currentStreak());
    }

    public function test_saving_again_updates_the_same_days_entry(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/write-journal', $this->payload());
        $this->actingAs($user)->post('/write-journal', $this->payload(['title' => 'Edited title']));

        $this->assertSame('Edited title', $user->journalEntries()->sole()->title);
    }

    public function test_drafts_do_not_count_until_published(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/write-journal', $this->payload(['action' => 'draft']))
            ->assertSessionHas('status', 'Draft saved. Publish it to secure your streak.');

        $this->assertTrue($user->journalEntries()->sole()->isDraft());
        $this->assertSame(0, $user->currentStreak());
    }

    public function test_saving_a_draft_does_not_unpublish_an_entry(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/write-journal', $this->payload());
        $this->actingAs($user)->post('/write-journal', $this->payload(['action' => 'draft']));

        $this->assertFalse($user->journalEntries()->sole()->isDraft());
    }

    public function test_drafts_may_be_empty_but_published_entries_need_content(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/write-journal', $this->payload(['content' => '', 'action' => 'draft']))
            ->assertSessionHasNoErrors();

        $this->actingAs($user)->post('/write-journal', $this->payload(['content' => '']))
            ->assertSessionHasErrors('content');
    }

    public function test_entries_cannot_be_dated_in_the_future(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/write-journal', $this->payload(['entry_date' => '2026-10-16']))
            ->assertSessionHasErrors('entry_date');

        $this->actingAs($user)->get('/write-journal?date=2026-10-16')
            ->assertSessionHasErrors('date');
    }

    public function test_past_days_can_be_written_and_reopened(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/write-journal', $this->payload(['entry_date' => '2026-10-14', 'title' => 'Yesterday']))
            ->assertRedirect(route('write-journal', ['date' => '2026-10-14']));

        $this->actingAs($user)->get('/write-journal?date=2026-10-14')
            ->assertOk()
            ->assertSee('Yesterday');
    }

    public function test_invalid_mood_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/write-journal', $this->payload(['mood' => 'ecstatic']))
            ->assertSessionHasErrors('mood');
    }

    public function test_users_only_see_their_own_entries(): void
    {
        $other = JournalEntry::factory()->on('2026-10-15')->create(['title' => 'Someone else\'s secret']);
        $user = User::factory()->create();

        $this->actingAs($user)->get('/write-journal')->assertDontSee('Someone else\'s secret');

        $this->actingAs($user)->post('/write-journal', $this->payload());
        $this->assertSame('Someone else\'s secret', $other->fresh()->title);
    }
}
