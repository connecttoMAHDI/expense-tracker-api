<?php

namespace Database\Factories;

use App\Enum\ExpenseCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'description' => implode(' ', $this->faker->words()),
            'amount' => $this->faker->numberBetween(0, 500),
            'category' => Arr::random(ExpenseCategory::cases())->value,
        ];
    }
}
