<?php

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
        $created = Carbon::now()->addDays(rand(-90, -15));
        $status = $this->faker->randomElement(StatusEnum::cases());
        return [
            'user_id' => rand(1,5),
            'parent_id' => Arr::random([null, rand(1,5), rand(3,9), rand(8,20), rand(9,25), rand(11,40)]),
            'status' => $status,
            'priority' => $this->faker->randomElement(PriorityEnum::cases()),
            'title' => $this->faker->realText(100, 3),
            'description' => $this->faker->realText(1000, 3),
            'created_at' => $created,
            'completed_at' => $status->value === 'done' ? $this->faker->dateTimeThisMonth() : null,
        ];
    }
}
