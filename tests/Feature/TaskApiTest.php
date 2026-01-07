<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_all_tasks(): void
    {
        $user = User::factory()->create();
        Task::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'user_id',
                    'title',
                    'description',
                    'status',
                    'created_at',
                    'updated_at',
                    'user' => [
                        'id',
                        'name',
                        'email',
                    ],
                ],
            ])
            ->assertJsonCount(3);
    }

    public function test_can_create_task(): void
    {
        $user = User::factory()->create();

        $taskData = [
            'title' => 'New Task',
            'description' => 'Task description',
            'status' => 'pending',
        ];

        $response = $this->actingAs($user)->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'user_id',
                'title',
                'description',
                'status',
                'user',
            ])
            ->assertJson([
                'title' => 'New Task',
                'description' => 'Task description',
                'status' => 'pending',
            ]);

        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'title' => 'New Task',
            'status' => 'pending',
        ]);
    }

    public function test_task_creation_requires_authentication(): void
    {
        $response = $this->postJson('/api/tasks', [
            'title' => 'New Task',
        ]);

        $response->assertStatus(401);
    }

    public function test_task_creation_requires_title(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/tasks', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    public function test_task_creation_validates_status(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/tasks', [
            'title' => 'New Task',
            'status' => 'invalid_status',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    public function test_cannot_create_duplicate_task_title_for_same_user(): void
    {
        $user = User::factory()->create();
        
        // Create first task
        Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Duplicate Task',
        ]);

        // Try to create another task with the same title for the same user
        $response = $this->actingAs($user)->postJson('/api/tasks', [
            'title' => 'Duplicate Task',
            'description' => 'This should fail',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    public function test_can_create_same_task_title_for_different_users(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        // Create task for user1
        Task::factory()->create([
            'user_id' => $user1->id,
            'title' => 'Same Title',
        ]);

        // Create task with same title for user2 (should succeed)
        $response = $this->actingAs($user2)->postJson('/api/tasks', [
            'title' => 'Same Title',
            'description' => 'This should succeed',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'title' => 'Same Title',
            ]);

        $this->assertDatabaseHas('tasks', [
            'user_id' => $user2->id,
            'title' => 'Same Title',
        ]);
    }

    public function test_can_show_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'user_id',
                'title',
                'description',
                'status',
                'user',
            ])
            ->assertJson([
                'id' => $task->id,
                'title' => $task->title,
            ]);
    }

    public function test_can_update_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Old Title',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)->putJson("/api/tasks/{$task->id}", [
            'title' => 'Updated Title',
            'status' => 'completed',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'title' => 'Updated Title',
                'status' => 'completed',
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Title',
            'status' => 'completed',
        ]);
    }

    public function test_can_delete_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Task deleted successfully']);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}

