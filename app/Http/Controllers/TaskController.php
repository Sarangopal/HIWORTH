<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Display a listing of the authenticated user's tasks.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        // Admins can see all tasks, regular users only see their own
        if ($user->isAdmin()) {
            $tasks = Task::with('user')->get();
        } else {
            $tasks = Task::where('user_id', $user->id)
                ->with('user')
                ->get();
        }

        return response()->json($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();
        $assignToUserId = $request->input('user_id');
        
        // Admins can assign to any user, regular users can only assign to themselves
        if ($user->isAdmin() && $assignToUserId) {
            $targetUserId = $assignToUserId;
            // Validate that the user exists
            User::findOrFail($targetUserId);
        } else {
            // Regular users always assign to themselves
            $targetUserId = $user->id;
        }

        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
                'unique:tasks,title,NULL,id,user_id,' . $targetUserId,
            ],
            'description' => 'nullable|string',
            'status' => 'nullable|in:pending,in_progress,completed',
            'user_id' => $user->isAdmin() ? 'nullable|exists:users,id' : 'nullable',
        ], [
            'title.unique' => 'This user already has a task with this title. Please choose a different title.',
            'user_id.exists' => 'Selected user does not exist.',
        ]);

        // Assign task to the selected user (or current user for regular users)
        $validated['user_id'] = $targetUserId;
        // Track who created the task
        $validated['created_by'] = $user->id;

        $task = Task::create($validated);
        $task->load(['user', 'creator']);

        return response()->json($task, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task): JsonResponse
    {
        $user = Auth::user();
        
        // Admins can view any task, regular users can only view their own
        if (!$user->isAdmin() && $task->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->load('user');
        return response()->json($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task): JsonResponse
    {
        $user = Auth::user();
        
        // Admins can update any task, regular users can only update their own
        if (!$user->isAdmin() && $task->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => [
                'sometimes',
                'string',
                'max:255',
                'unique:tasks,title,' . $task->id . ',id,user_id,' . Auth::id(),
            ],
            'description' => 'nullable|string',
            'status' => 'sometimes|in:pending,in_progress,completed',
        ], [
            'title.unique' => 'You already have a task with this title. Please choose a different title.',
        ]);

        $task->update($validated);
        $task->load('user');

        return response()->json($task);
    }

    /**
     * Update task status.
     */
    public function updateStatus(Request $request, Task $task): JsonResponse
    {
        $user = Auth::user();
        
        // Admins can update any task, regular users can only update their own
        if (!$user->isAdmin() && $task->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $task->update($validated);
        $task->load('user');

        return response()->json($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task): JsonResponse
    {
        $user = Auth::user();
        
        // Admins can delete any task, regular users can only delete their own
        if (!$user->isAdmin() && $task->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->delete();
        return response()->json(['message' => 'Task deleted successfully']);
    }
}
