<?php

declare(strict_types = 1);

namespace Database\Factories;

use App\Enums\PriorityEnum;
use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $parentId = Arr::random([null, rand(1,5), rand(3,9), rand(8,20), rand(9,25), rand(11,40)]);
        return [
            'user_id' => rand(1,6),
            'parent_id' => $parentId,
            'status' => StatusEnum::TODO,
            'priority' => $this->faker->randomElement(PriorityEnum::cases()),
            'title' => $this->faker->realText(100, 3),
            'description' => $this->faker->realText(500, 3),
            'created_at' => $this->faker->dateTimeBetween('-5 months', '-1 weeks'),
            'completed_at' => null,
        ];
    }
}
