<?php

namespace Tests\Feature;

use App\Enums\PriorityEnum;
use App\Enums\StatusEnum;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user for testing with Sanctum authentication
        Sanctum::actingAs(User::factory()->create());
    }

    public function test_can_create_task()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $taskData = [
            'title' => 'Test Task',
            'description' => 'Test Description',
            // Optionally add status and priority if needed
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => StatusEnum::TODO->value, // Default status
            'priority' => PriorityEnum::LOW->value // Default priority
        ]);
    }

    public function test_user_can_get_own_tasks()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        // Create tasks for both users
        Task::factory()->count(5)->create(['user_id' => $user->id]);
        Task::factory()->count(3)->create(['user_id' => $otherUser->id]);

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data'); // Adjust based on your response structure
    }


    public function test_user_can_get_filtered_tasks()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        // Create tasks with different statuses for both users
        Task::factory()->count(3)->create(['user_id' => $user->id, 'status' => StatusEnum::TODO]);
        Task::factory()->count(2)->create(['user_id' => $user->id, 'status' => StatusEnum::DONE]);
        Task::factory()->count(1)->create(['user_id' => $otherUser->id, 'status' => StatusEnum::TODO]);

        $response = $this->getJson('/api/tasks/filtered?status=todo');

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }


    public function test_user_can_get_sorted_tasks()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        // Create tasks with different priorities for both users
        Task::factory()->create(['user_id' => $user->id, 'priority' => PriorityEnum::HIGH->value]);
        Task::factory()->create(['user_id' => $user->id, 'priority' => PriorityEnum::LOW->value]);
        Task::factory()->create(['user_id' => $user->id, 'priority' => PriorityEnum::MID->value]);
        Task::factory()->create(['user_id' => $otherUser->id, 'priority' => PriorityEnum::HIGH->value]);

        $response = $this->getJson('/api/tasks/sorted?priority=asc');

        $response->assertStatus(200);
        $tasks = $response->json('data');

        $this->assertEquals(PriorityEnum::HIGH->value, $tasks[0]['priority']);
        $this->assertEquals(PriorityEnum::MID->value, $tasks[1]['priority']);
        $this->assertEquals(PriorityEnum::LOW->value, $tasks[2]['priority']);
    }


    public function test_user_can_update_own_task()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $task = Task::factory()->create(['user_id' => $user->id]);
        Task::factory()->create(['user_id' => $otherUser->id]);

        $updateData = [
            'title' => 'Updated Task Title',
            'description' => 'Updated Task Description',
        ];

        $response = $this->patchJson('/api/tasks/' . $task->id, $updateData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Task Title',
            'description' => 'Updated Task Description'
        ]);
    }


    public function test_user_can_delete_own_task()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $task = Task::factory()->create(['user_id' => $user->id]);
        Task::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->deleteJson('/api/tasks/' . $task->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id
        ]);
    }


    public function test_user_cannot_delete_completed_task()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $task = Task::factory()->create(['user_id' => $user->id, 'status' => StatusEnum::DONE]);

        $response = $this->deleteJson('/api/tasks/' . $task->id);

        $response->assertStatus(422);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id
        ]);
    }


    public function test_user_can_mark_own_task_as_done()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $task = Task::factory()->create(['user_id' => $user->id, 'status' => StatusEnum::TODO]);
        Task::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->patchJson('/api/tasks/done/' . $task->id);

        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => StatusEnum::DONE->value
        ]);
    }


    public function test_cannot_mark_task_as_done_with_uncompleted_subtasks()
    {
        $task = Task::factory()->create(['status' => 'todo']);
        $subtask = Task::factory()->create(['parent_id' => $task->id, 'status' => 'todo']);

        $response = $this->patchJson("/api/tasks/done/{$task->id}");

        $response->assertStatus(403);
        $this->assertNull(Task::find($task->id)->completed_at);
    }

    public function test_cannot_update_other_users_task()
    {
        $task = Task::factory()->create();
        Sanctum::actingAs(User::factory()->create());

        $response = $this->patchJson("/api/tasks/{$task->id}", [
            'title' => 'Updated Task Title',
        ]);

        $response->assertStatus(403);
        $this->assertNotEquals('Updated Task Title', Task::find($task->id)->title);
    }
}
