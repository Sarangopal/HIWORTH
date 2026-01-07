<?php

namespace App\Http\Controllers;

use App\Models\Task;
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
        // Users can only see their own tasks
        $tasks = Task::where('user_id', Auth::id())
            ->with('user')
            ->get();

        return response()->json($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
                'unique:tasks,title,NULL,id,user_id,' . Auth::id(),
            ],
            'description' => 'nullable|string',
            'status' => 'nullable|in:pending,in_progress,completed',
        ], [
            'title.unique' => 'You already have a task with this title. Please choose a different title.',
        ]);

        // Automatically assign task to authenticated user
        $validated['user_id'] = Auth::id();

        $task = Task::create($validated);
        $task->load('user');

        return response()->json($task, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task): JsonResponse
    {
        // Check authorization - user can only view their own tasks
        if ($task->user_id !== Auth::id()) {
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
        // Check authorization - user can only update their own tasks
        if ($task->user_id !== Auth::id()) {
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
        // Check authorization - user can only update their own tasks
        if ($task->user_id !== Auth::id()) {
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
        // Check authorization - user can only delete their own tasks
        if ($task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->delete();
        return response()->json(['message' => 'Task deleted successfully']);
    }
}
