<?php

namespace Tests\Feature;

use App\Enums\Mood;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfilePageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-10-15 20:00:00');
    }

    /**
     * A real 1x1 PNG (UploadedFile::fake()->image() needs the GD extension).
     */
    private function portrait(string $name): UploadedFile
    {
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');

        return UploadedFile::fake()->createWithContent($name, $png);
    }

    private function persona(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Sabrian',
            'full_name' => 'Sabrian Zuhair',
            'handle' => '@sabrian_stories',
            'email' => 'sabrian@example.com',
            'affiliation' => 'Northwood University',
            'bio' => 'One day, one story.',
        ], $overrides);
    }

    public function test_profile_page_shows_identity_and_real_stats(): void
    {
        $user = User::factory()->create([
            'name' => 'Sabrian',
            'handle' => 'sabrian_stories',
            'affiliation' => 'Northwood University',
            'bio' => 'Late-night code breakthroughs.',
            'created_at' => '2026-10-01 10:00:00',
        ]);

        // Best run Oct 1-4 (4 days), current run Oct 13-15 (3 days); all written at 9 PM
        foreach (['2026-10-01', '2026-10-02', '2026-10-03', '2026-10-04', '2026-10-13', '2026-10-14', '2026-10-15'] as $date) {
            JournalEntry::factory()->for($user)->on($date)->create([
                'content' => str_repeat('word ', 100),
                'created_at' => $date.' 21:10:00',
            ]);
        }

        $this->actingAs($user)->get('/profile')
            ->assertOk()
            ->assertSee('Sabrian')
            ->assertSee('@sabrian_stories')
            ->assertSee('Northwood University')
            ->assertSee('“Late-night code breakthroughs.”', false)
            ->assertSee('7 Stories')
            ->assertSee('700 words penned across 3 weeks')
            ->assertSee('Avg 100 w/entry')
            ->assertSee('3 Days')
            ->assertSee('1-Week badge unlocks in 4 days')
            ->assertSee('Achieved Oct 1 – Oct 4, 2026', false)
            ->assertSee('46.7%')
            ->assertSee('Preferred: 9:00 PM – 10:00 PM', false)
            ->assertSee('Active Dusk')
            ->assertSee('Dusk Chronicler')
            ->assertSee('October 1, 2026 · First Volume', false)
            ->assertSee('15 Days Active');
    }

    public function test_profile_page_handles_a_brand_new_user(): void
    {
        $user = User::factory()->create(['name' => 'New Writer']);

        $this->actingAs($user)->get('/profile')
            ->assertOk()
            ->assertSee('0 Stories')
            ->assertSee('Your first run starts with one entry.')
            ->assertSee('Write a few entries to find your rhythm.')
            ->assertSee('Not set')
            ->assertSee('NW')
            ->assertDontSee('Active Dusk');
    }

    public function test_persona_can_be_updated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->patch('/profile', $this->persona())
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile')
            ->assertSessionHas('status', 'profile-updated');

        $user->refresh();
        $this->assertSame('Sabrian', $user->name);
        $this->assertSame('Sabrian Zuhair', $user->full_name);
        $this->assertSame('sabrian_stories', $user->handle);
        $this->assertSame('Northwood University', $user->affiliation);
        $this->assertSame('One day, one story.', $user->bio);
    }

    public function test_handle_must_be_unique_and_simple(): void
    {
        User::factory()->create(['handle' => 'taken']);
        $user = User::factory()->create();

        $this->actingAs($user)->patch('/profile', $this->persona(['handle' => '@taken']))
            ->assertSessionHasErrors('handle');

        $this->actingAs($user)->patch('/profile', $this->persona(['handle' => 'has spaces!']))
            ->assertSessionHasErrors('handle');
    }

    public function test_bio_is_limited_to_180_characters(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->patch('/profile', $this->persona(['bio' => str_repeat('a', 181)]))
            ->assertSessionHasErrors('bio');
    }

    public function test_portrait_can_be_uploaded_replaced_and_removed(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $this->actingAs($user)->patch('/profile', $this->persona(['avatar' => $this->portrait('me.png')]));
        $first = $user->refresh()->avatar_path;
        Storage::disk('public')->assertExists($first);
        $this->assertStringContainsString('storage/avatars/', $user->avatarUrl());

        $this->actingAs($user)->patch('/profile', $this->persona(['avatar' => $this->portrait('new.png')]));
        $second = $user->refresh()->avatar_path;
        Storage::disk('public')->assertMissing($first);
        Storage::disk('public')->assertExists($second);

        $this->actingAs($user)->patch('/profile', $this->persona(['remove_avatar' => '1']));
        Storage::disk('public')->assertMissing($second);
        $this->assertNull($user->refresh()->avatar_path);
        $this->assertNull($user->avatarUrl());
    }

    public function test_portrait_must_be_an_image(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $this->actingAs($user)->patch('/profile', $this->persona(['avatar' => UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf')]))
            ->assertSessionHasErrors('avatar');
    }

    public function test_saving_without_a_new_portrait_keeps_the_old_one(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $this->actingAs($user)->patch('/profile', $this->persona(['avatar' => $this->portrait('me.png')]));
        $path = $user->refresh()->avatar_path;

        $this->actingAs($user)->patch('/profile', $this->persona(['bio' => 'Changed']));

        $this->assertSame($path, $user->refresh()->avatar_path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_export_downloads_published_entries_as_markdown(): void
    {
        $user = User::factory()->create(['name' => 'Sabrian']);
        JournalEntry::factory()->for($user)->on('2026-10-14')->create(['title' => 'Rainy walk', 'content' => 'Puddles everywhere.', 'mood' => Mood::Happy]);
        JournalEntry::factory()->for($user)->on('2026-10-15')->draft()->create(['title' => 'Secret draft']);
        JournalEntry::factory()->on('2026-10-13')->create(['title' => 'Not mine']);

        $response = $this->actingAs($user)->get('/profile/export');

        $response->assertOk()->assertDownload('daily-journal-2026-10-15.md');
        $markdown = $response->streamedContent();
        $this->assertStringContainsString("# Sabrian's Daily Journal", $markdown);
        $this->assertStringContainsString('## Rainy walk', $markdown);
        $this->assertStringContainsString('*Wednesday, October 14, 2026 · Happy 😊*', $markdown);
        $this->assertStringContainsString('Puddles everywhere.', $markdown);
        $this->assertStringNotContainsString('Secret draft', $markdown);
        $this->assertStringNotContainsString('Not mine', $markdown);
    }

    public function test_password_errors_are_shown_on_the_profile_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from('/profile')
            ->followingRedirects()
            ->put('/password', [
                'current_password' => 'wrong-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertSee('The password is incorrect.');
    }

    public function test_longest_streak_run_reports_its_dates(): void
    {
        $user = User::factory()->create();
        foreach (['2026-09-01', '2026-09-02', '2026-09-10', '2026-09-11', '2026-09-12'] as $date) {
            JournalEntry::factory()->for($user)->on($date)->create();
        }

        $run = $user->longestStreakRun();

        $this->assertSame(3, $run['days']);
        $this->assertSame('2026-09-10', $run['start']->toDateString());
        $this->assertSame('2026-09-12', $run['end']->toDateString());
        $this->assertNull(User::factory()->create()->longestStreakRun());
    }
}
