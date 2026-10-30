<?php

namespace Database\Factories;

use App\Models\Era;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Quote>
 */
class QuoteFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'era_id' => Era::query()->inRandomOrder()->value('id'),
            'content' => fake()->sentence(),
            'poet' => fake()->name(),
        ];
    }
}
