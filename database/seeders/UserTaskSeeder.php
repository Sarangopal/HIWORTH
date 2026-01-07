<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Task;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample users
        $user1 = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
        ]);

        $user2 = User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => Hash::make('password'),
        ]);

        $user3 = User::create([
            'name' => 'Bob Johnson',
            'email' => 'bob@example.com',
            'password' => Hash::make('password'),
        ]);

        // Create tasks for user1
        Task::create([
            'user_id' => $user1->id,
            'title' => 'Complete project documentation',
            'description' => 'Write comprehensive documentation for the new feature',
            'status' => 'in_progress',
        ]);

        Task::create([
            'user_id' => $user1->id,
            'title' => 'Review code changes',
            'description' => 'Review pull requests from the team',
            'status' => 'pending',
        ]);

        Task::create([
            'user_id' => $user1->id,
            'title' => 'Deploy to production',
            'description' => 'Deploy the latest version to production server',
            'status' => 'completed',
        ]);

        // Create tasks for user2
        Task::create([
            'user_id' => $user2->id,
            'title' => 'Design new UI components',
            'description' => 'Create mockups for the dashboard redesign',
            'status' => 'in_progress',
        ]);

        Task::create([
            'user_id' => $user2->id,
            'title' => 'Update user guide',
            'description' => 'Update the user guide with new features',
            'status' => 'pending',
        ]);

        // Create tasks for user3
        Task::create([
            'user_id' => $user3->id,
            'title' => 'Fix bug in login system',
            'description' => 'Investigate and fix the authentication issue',
            'status' => 'completed',
        ]);

        Task::create([
            'user_id' => $user3->id,
            'title' => 'Optimize database queries',
            'description' => 'Review and optimize slow database queries',
            'status' => 'pending',
        ]);
    }
}
