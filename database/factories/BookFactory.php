<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory for generating realistic fake Book records.
 * Used in tests (RefreshDatabase) and the BookSeeder for random fixture data.
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            // Generate a plausible book-style title (2-4 words)
            'title'        => ucwords(implode(' ', $this->faker->words(rand(2, 4)))),
            'publisher'    => $this->faker->company(),
            'author'       => $this->faker->name(),
            'genre'        => $this->faker->randomElement([
                'Fiction', 'Non-Fiction', 'Science Fiction', 'Fantasy',
                'Mystery', 'Thriller', 'Romance', 'Biography',
                'History', 'Self-Help', 'Horror',
            ]),
            // Random historical publication date up to the current year
            'published_at' => $this->faker->dateTimeBetween('-100 years', 'now')->format('Y-m-d'),
            // Typical book length: 10k–200k words
            'word_count'   => $this->faker->numberBetween(10_000, 200_000),
            // Typical retail book price: $5.99–$49.99
            'price'        => $this->faker->randomFloat(2, 5.99, 49.99),
        ];
    }
}
