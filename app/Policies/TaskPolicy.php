<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Determine if the user can view the task.
     */
    public function view(User $user, Task $task): bool
    {
        // Admins can view any task, regular users can only view their own
        return $user->isAdmin() || $user->id === $task->user_id;
    }

    /**
     * Determine if the user can update the task.
     */
    public function update(User $user, Task $task): bool
    {
        // Admins can update any task, regular users can only update their own
        return $user->isAdmin() || $user->id === $task->user_id;
    }

    /**
     * Determine if the user can delete the task.
     */
    public function delete(User $user, Task $task): bool
    {
        // Admins can delete any task, regular users can only delete their own
        return $user->isAdmin() || $user->id === $task->user_id;
    }
}
