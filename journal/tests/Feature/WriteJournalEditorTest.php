<?php

namespace Tests\Feature;

use App\Models\JournalEntry;
use App\Models\User;
use App\Support\RichText;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WriteJournalEditorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-10-15 20:00:00');
        Storage::fake('public');
    }

    /**
     * A real 1x1 PNG (UploadedFile::fake()->image() needs the GD extension).
     */
    private function photo(string $name = 'moment.png'): UploadedFile
    {
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');

        return UploadedFile::fake()->createWithContent($name, $png);
    }

    private function save(User $user, array $overrides = [])
    {
        return $this->actingAs($user)->post('/write-journal', array_merge([
            'entry_date' => '2026-10-15',
            'title' => 'Tonight',
            'content' => '<div>Plain words</div>',
            'action' => 'publish',
        ], $overrides));
    }

    // ---------- Toolbar ----------

    public function test_toolbar_has_bold_italic_and_add_image_only(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/write-journal')
            ->assertOk()
            ->assertSee('data-format="bold"', false)
            ->assertSee('data-format="italic"', false)
            ->assertSee('Add Image')
            ->assertDontSee('Voice Note')
            ->assertDontSee('format_quote')
            ->assertDontSee('format_list_bulleted')
            ->assertDontSee('horizontal_rule');
    }

    // ---------- Formatting ----------

    public function test_bold_and_italic_are_kept_and_everything_else_is_stripped(): void
    {
        $user = User::factory()->create();

        $this->save($user, [
            'content' => '<div><b>Brave</b> and <i>quiet</i></div><script>alert(1)</script>'
                .'<div onclick="steal()"><a href="https://evil.test">a link</a> <img src=x onerror=alert(1)></div>',
        ])->assertSessionHasNoErrors();

        $content = $user->journalEntries()->sole()->content;
        $this->assertStringContainsString('<b>Brave</b>', $content);
        $this->assertStringContainsString('<i>quiet</i>', $content);
        $this->assertStringContainsString('a link', $content);
        $this->assertStringNotContainsString('script', $content);
        $this->assertStringNotContainsString('onclick', $content);
        $this->assertStringNotContainsString('<a', $content);
        $this->assertStringNotContainsString('<img', $content);
    }

    public function test_formatting_without_words_counts_as_empty(): void
    {
        $user = User::factory()->create();

        $this->save($user, ['content' => '<div><br></div><b> </b>'])
            ->assertSessionHasErrors('content');

        $this->save($user, ['content' => '<div><br></div>', 'action' => 'draft'])
            ->assertSessionHasNoErrors();
        $this->assertNull($user->journalEntries()->sole()->content);
    }

    public function test_word_count_and_excerpt_read_through_the_markup(): void
    {
        $user = User::factory()->create();

        $this->save($user, ['content' => '<div>One <b>two</b></div><div>three</div><p>four</p>']);

        $entry = $user->journalEntries()->sole();
        $this->assertSame(4, $entry->word_count);
        $this->assertSame('One two three four', $entry->excerpt());
    }

    public function test_saved_formatting_is_shown_in_the_editor(): void
    {
        $user = User::factory()->create();
        $this->save($user, ['content' => '<div><b>Brave</b> words</div>']);

        $this->actingAs($user)->get('/write-journal')
            ->assertSee('<div><b>Brave</b> words</div>', false);
    }

    public function test_older_plain_text_entries_are_escaped_and_keep_line_breaks(): void
    {
        $user = User::factory()->create();
        JournalEntry::factory()->for($user)->on('2026-10-15')->create(['content' => "Tom & <Jerry>\nSecond line"]);

        $this->actingAs($user)->get('/write-journal')
            ->assertSee('Tom &amp; &lt;Jerry&gt;<br>', false)
            ->assertSee('Second line');
    }

    public function test_rich_text_converts_to_markdown_for_exports(): void
    {
        $this->assertSame(
            "**Bold** and *italic*\n\nNext paragraph",
            RichText::toMarkdown('<p><b>Bold</b> and <i>italic</i></p><p>Next paragraph</p>'),
        );
        $this->assertSame('Just text', RichText::toMarkdown('Just text'));
    }

    // ---------- Images ----------

    public function test_images_are_uploaded_with_the_entry_and_shown_on_the_page(): void
    {
        $user = User::factory()->create();

        $this->save($user, ['images' => [$this->photo('a.png'), $this->photo('b.png')]])
            ->assertSessionHasNoErrors();

        $images = $user->journalEntries()->sole()->images;
        $this->assertCount(2, $images);
        foreach ($images as $image) {
            Storage::disk('public')->assertExists($image->path);
            $this->assertStringStartsWith("entries/{$user->id}/", $image->path);
        }

        $this->actingAs($user)->get('/write-journal')
            ->assertSee($images[0]->url(), false)
            ->assertSee('data-remove-saved="'.$images[0]->id.'"', false)
            ->assertDontSee('Library Walk');
    }

    public function test_images_can_be_added_to_a_draft_without_text(): void
    {
        $user = User::factory()->create();

        $this->save($user, ['content' => '', 'action' => 'draft', 'images' => [$this->photo()]])
            ->assertSessionHasNoErrors();

        $this->assertCount(1, $user->journalEntries()->sole()->images);
    }

    public function test_removing_an_image_deletes_the_file(): void
    {
        $user = User::factory()->create();
        $this->save($user, ['images' => [$this->photo('a.png'), $this->photo('b.png')]]);
        [$first, $second] = $user->journalEntries()->sole()->images;

        $this->save($user, ['remove_images' => [$first->id]]);

        $this->assertModelMissing($first);
        Storage::disk('public')->assertMissing($first->path);
        $this->assertModelExists($second);
    }

    public function test_images_of_other_users_cannot_be_removed(): void
    {
        $owner = User::factory()->create();
        $this->save($owner, ['images' => [$this->photo()]]);
        $theirs = $owner->journalEntries()->sole()->images->first();

        $this->save(User::factory()->create(), ['remove_images' => [$theirs->id]]);

        $this->assertModelExists($theirs);
        Storage::disk('public')->assertExists($theirs->path);
    }

    public function test_an_entry_holds_at_most_six_images(): void
    {
        $user = User::factory()->create();
        $this->save($user, ['images' => array_map(fn ($i) => $this->photo("$i.png"), range(1, 5))]);

        $this->save($user, ['images' => [$this->photo('6.png'), $this->photo('7.png')]])
            ->assertSessionHasErrors('images');
        $this->assertCount(5, $user->journalEntries()->sole()->images);

        // Removing one makes room again
        $removeId = $user->journalEntries()->sole()->images->first()->id;
        $this->save($user, ['remove_images' => [$removeId], 'images' => [$this->photo('6.png'), $this->photo('7.png')]])
            ->assertSessionHasNoErrors();
        $this->assertCount(6, $user->journalEntries()->sole()->images);
    }

    public function test_only_images_are_accepted(): void
    {
        $user = User::factory()->create();

        $this->save($user, ['images' => [UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf')]])
            ->assertSessionHasErrors('images.0');
    }

    public function test_dashboard_uses_the_entrys_first_photo_as_its_cover(): void
    {
        $user = User::factory()->create();
        $this->save($user, ['images' => [$this->photo()]]);
        $image = $user->journalEntries()->sole()->images->first();

        $this->actingAs($user)->get('/dashboard')->assertSee($image->url(), false);
    }

    public function test_deleting_the_account_removes_uploaded_photos(): void
    {
        $user = User::factory()->create();
        $this->save($user, ['images' => [$this->photo()]]);
        $path = $user->journalEntries()->sole()->images->first()->path;

        $this->actingAs($user)->delete('/profile', ['password' => 'password'])->assertRedirect('/');

        Storage::disk('public')->assertMissing($path);
    }
}
