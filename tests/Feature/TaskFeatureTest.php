<?php

namespace Tests\Feature;

use App\Enums\PriorityEnum;
use App\Enums\StatusEnum;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function testCanCreateTask()
    {
        $taskData = [
            'title' => 'Test Task',
            'description' => 'Test Description',
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('tasks', [
            'user_id' => $this->user->id,
            'title' => $taskData['title'],
            'description' => $taskData['description'],
            'status' => StatusEnum::TODO->value, // Default status
            'priority' => PriorityEnum::LOW->value // Default priority
        ]);
    }

    public function testUserCanGetOwnTasks()
    {
        // Create tasks for both users
        Task::factory()->count(5)->create(['user_id' => $this->user->id]);
        Task::factory()->count(3)->create(['user_id' => $this->otherUser->id]);

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data');
    }


    public function testUserCanGetFilteredTasks()
    {
        // Create tasks with different statuses for both users
        Task::factory()->count(3)->create(['user_id' => $this->user->id, ]);
        Task::factory()->count(2)->create(['user_id' => $this->user->id, 'status' => StatusEnum::DONE]);
        Task::factory()->count(1)->create(['user_id' => $this->otherUser->id, ]);

        $response = $this->getJson('/api/tasks/filtered?status=todo');

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }


    public function testUserCanGetSortedTasks()
    {
        // Create tasks with different priorities for both users
        Task::factory()->create(['user_id' => $this->user->id, 'priority' => PriorityEnum::HIGH->value]);
        Task::factory()->create(['user_id' => $this->user->id, 'priority' => PriorityEnum::LOW->value]);
        Task::factory()->create(['user_id' => $this->user->id, 'priority' => PriorityEnum::MID->value]);
        Task::factory()->create(['user_id' => $this->otherUser->id, 'priority' => PriorityEnum::HIGH->value]);

        $response = $this->getJson('/api/tasks/sorted?priority=asc');

        $response->assertStatus(200);
        $tasks = $response->json('data');

        $this->assertEquals(PriorityEnum::HIGH->value, $tasks[0]['priority']);
        $this->assertEquals(PriorityEnum::MID->value, $tasks[1]['priority']);
        $this->assertEquals(PriorityEnum::LOW->value, $tasks[2]['priority']);
    }


    public function testUserCanUpdateOwnTask()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);
        Task::factory()->create(['user_id' => $this->otherUser->id]);

        $updateData = [
            'title' => 'Updated Task Title',
            'description' => 'Updated Task Description',
        ];

        $response = $this->patchJson('/api/tasks/' . $task->id, $updateData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => $updateData['title'],
            'description' => $updateData['description'],
        ]);
    }


    public function testUserCanDeleteOwnTask()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id, ]);
        Task::factory()->create(['user_id' => $this->otherUser->id]);

        $response = $this->deleteJson('/api/tasks/' . $task->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id
        ]);
    }

    public function testUserCannotDeleteOthersTask()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id, ]);
        $otherTask = Task::factory()->create(['user_id' => $this->otherUser->id, ]);

        $response = $this->deleteJson('/api/tasks/' . $otherTask->id);

        $response->assertStatus(403);
        $this->assertDatabaseHas('tasks', [
            'id' => $otherTask->id
        ]);
    }


    public function testUserCannotDeleteCompletedTask()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id, 'status' => StatusEnum::DONE]);

        $response = $this->deleteJson('/api/tasks/' . $task->id);

        $response->assertStatus(409);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id
        ]);
    }


    public function testUserCanMarkOwnTaskAsDone()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id, ]);
        Task::factory()->create(['user_id' => $this->otherUser->id]);

        $response = $this->patchJson('/api/tasks/done/' . $task->id);

        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => StatusEnum::DONE->value
        ]);
    }


    public function testCannotMarkTaskAsDoneWithUncompletedSubtasks()
    {
        $task = Task::factory()->create(['status' => 'todo', ]);
        $subtask = Task::factory()->create(['parent_id' => $task->id, 'status' => 'todo']);

        $response = $this->patchJson("/api/tasks/done/{$task->id}");

        $response->assertStatus(403);
        $this->assertNull(Task::find($task->id)->completed_at);
    }

    public function testCannotUpdateOtherUsersTask()
    {
        $task = Task::factory()->create(['user_id' => $this->otherUser->id]);

        $response = $this->patchJson("/api/tasks/{$task->id}", [
            'title' => 'Updated Task Title',
        ]);

        $response->assertStatus(403);
        $this->assertNotEquals('Updated Task Title', Task::find($task->id)->title);
    }

    public function testUserGetOwnTasksInTree()
    {
        // Create a root task for the authenticated user
        $rootTask = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Root Task'
        ]);

        // Create children tasks for the root task
        $childTask1 = Task::factory()->create([
            'user_id' => $this->user->id,
            'parent_id' => $rootTask->id,
            'title' => 'Child Task 1'
        ]);

        $childTask2 = Task::factory()->create([
            'user_id' => $this->user->id,
            'parent_id' => $rootTask->id,
            'title' => 'Child Task 2'
        ]);

        // Create a grandchild task for one of the children
        $grandChildTask = Task::factory()->create([
            'user_id' => $this->user->id,
            'parent_id' => $childTask1->id,
            'title' => 'Grandchild Task'
        ]);

        // Request the task tree starting from the root task
        $response = $this->getJson("/api/tasks/tree/{$rootTask->id}");

        // Assert the response structure and data
        $response->assertStatus(200)
            ->assertJsonStructure([
                [
                    'id',
                    'title',
                    'children' => [
                        [
                            'id',
                            'title',
                            'children' => [
                                [
                                    'id',
                                    'title',
                                    'children'
                                ]
                            ]
                        ],
                        [
                            'id',
                            'title',
                            'children'
                        ]
                    ]
                ]
            ])
            ->assertJson([
                [
                    'id' => $rootTask->id,
                    'title' => $rootTask['title'],
                    'children' => [
                        [
                            'id' => $childTask1->id,
                            'title' => $childTask1['title'],
                            'children' => [
                                [
                                    'id' => $grandChildTask->id,
                                    'title' => $grandChildTask['title'],
                                    'children' => []
                                ]
                            ]
                        ],
                        [
                            'id' => $childTask2->id,
                            'title' => $childTask2['title'],
                            'children' => []
                        ]
                    ]
                ]
            ]);
    }
}
