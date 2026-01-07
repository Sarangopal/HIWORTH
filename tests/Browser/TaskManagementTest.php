<?php

namespace Tests\Browser;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TaskManagementTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_can_create_task(): void
    {
        $user = User::factory()->create(['name' => 'John Doe']);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/tasks/create')
                ->assertSee('Create New Task')
                ->select('user_id', $user->id)
                ->type('title', 'Complete project documentation')
                ->type('description', 'Write comprehensive documentation for the new feature')
                ->select('status', 'pending')
                ->press('Create Task')
                ->assertPathIs('/tasks')
                ->assertSee('Task created successfully')
                ->assertSee('Complete project documentation')
                ->assertSee('John Doe');
        });
    }

    public function test_validation_errors_display_when_creating_task(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/tasks/create')
                ->press('Create Task')
                ->assertSee('The user id field is required')
                ->assertSee('The title field is required');
        });
    }

    public function test_can_view_tasks_for_specific_user(): void
    {
        $user = User::factory()->create(['name' => 'Jane Smith']);
        Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'User Task 1',
            'status' => 'pending',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit("/users/{$user->id}")
                ->assertSee('Jane Smith')
                ->assertSee('User Task 1')
                ->assertSee('Pending');
        });
    }

    public function test_can_update_task_status(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Test Task',
            'status' => 'pending',
        ]);

        $this->browse(function (Browser $browser) use ($task) {
            $browser->visit("/users/{$task->user_id}")
                ->assertSee('Test Task')
                ->select("select[name='status']", 'completed')
                ->pause(1500) // Wait for form submission
                ->assertSee('Task status updated successfully');
        });
    }

    public function test_can_delete_task(): void
    {
        $user = User::factory()->create(['name' => 'Test User']);
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Task to Delete',
        ]);

        $this->browse(function (Browser $browser) use ($task, $user) {
            $browser->visit("/users/{$user->id}")
                ->assertSee('Task to Delete')
                ->press('Delete')
                ->acceptDialog()
                ->pause(1000) // Wait for deletion
                ->assertSee('Task deleted successfully')
                ->assertDontSee('Task to Delete');
        });
    }

    public function test_can_filter_tasks_by_user(): void
    {
        $user1 = User::factory()->create(['name' => 'User One']);
        $user2 = User::factory()->create(['name' => 'User Two']);
        
        Task::factory()->create(['user_id' => $user1->id, 'title' => 'Task for User One']);
        Task::factory()->create(['user_id' => $user2->id, 'title' => 'Task for User Two']);

        $this->browse(function (Browser $browser) use ($user1, $user2) {
            $browser->visit('/tasks')
                ->assertSee('Task for User One')
                ->assertSee('Task for User Two')
                ->select('user_id', $user1->id)
                ->pause(1000) // Wait for filter to apply
                ->assertSee('Task for User One')
                ->assertDontSee('Task for User Two');
        });
    }

    public function test_can_navigate_between_pages(): void
    {
        $this->browse(function (Browser $browser) {
            // Start at users page
            $browser->visit('/users')
                ->assertSee('Users Management')
                ->clickLink('Tasks')
                ->assertPathIs('/tasks')
                ->assertSee('Tasks Management')
                ->clickLink('Users')
                ->assertPathIs('/users')
                ->assertSee('Users Management');
        });
    }
}

