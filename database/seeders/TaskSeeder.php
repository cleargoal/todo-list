<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 40; $i++) {
            $task = Task::factory()->create([
                'user_id' => 1,
                'parent_id' => $i === 0 ? null : rand(1, $i),
            ]);
        }

        Task::factory(60)->create();
    }
}
