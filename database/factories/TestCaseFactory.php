<?php

namespace Database\Factories;

use App\Models\TestCase;
use Illuminate\Database\Eloquent\Factories\Factory;

class TestCaseFactory extends Factory
{
    protected $model = TestCase::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'module' => fake()->randomElement([
                'Authentication',
                'Dashboard',
                'Profile',
                'Testing',
            ]),
            'description' => fake()->sentence(),
            'priority' => fake()->randomElement([
                'low',
                'medium',
                'high',
            ]),
            'status' => fake()->randomElement([
                'active',
                'inactive',
            ]),
        ];
    }
}