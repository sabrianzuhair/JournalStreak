<?php

namespace Database\Factories;

use App\Enums\Mood;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JournalEntry>
 */
class JournalEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'entry_date' => fake()->unique()->dateTimeBetween('-60 days', 'now')->format('Y-m-d'),
            'title' => rtrim(fake()->sentence(5), '.'),
            'content' => fake()->paragraphs(3, true),
            'mood' => fake()->randomElement(Mood::cases()),
            'tags' => fake()->randomElements(['CampusLife', 'Reflections', 'Habits', 'Gratitude', 'Fitness', 'Study'], 2),
            'status' => JournalEntry::STATUS_PUBLISHED,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => JournalEntry::STATUS_DRAFT]);
    }

    public function on(string $date): static
    {
        return $this->state(fn () => ['entry_date' => $date]);
    }
}
