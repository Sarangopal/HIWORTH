<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_task_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $task->user);
        $this->assertEquals($user->id, $task->user->id);
    }

    public function test_task_can_be_created(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Test Task',
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'user_id' => $user->id,
            'title' => 'Test Task',
            'status' => 'pending',
        ]);
    }

    public function test_task_has_valid_status(): void
    {
        $user = User::factory()->create();
        
        $validStatuses = ['pending', 'in_progress', 'completed'];
        
        foreach ($validStatuses as $status) {
            $task = Task::factory()->create([
                'user_id' => $user->id,
                'status' => $status,
            ]);
            
            $this->assertEquals($status, $task->status);
        }
    }

    public function test_task_can_have_null_description(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'description' => null,
        ]);

        $this->assertNull($task->description);
    }

    public function test_task_deleted_when_user_deleted(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $taskId = $task->id;
        $user->delete();

        $this->assertDatabaseMissing('tasks', ['id' => $taskId]);
    }
}

