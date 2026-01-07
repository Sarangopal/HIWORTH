<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class TaskController extends Controller
{
    /**
     * Display a listing of the authenticated user's tasks.
     */
    public function index(Request $request): View
    {
        // Users can only see their own tasks
        $tasks = Task::where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('tasks.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new task.
     */
    public function create(): View
    {
        return view('tasks.create');
    }

    /**
     * Store a newly created task in storage.
     */
    public function store(Request $request): RedirectResponse
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

        Task::create($validated);

        return redirect()->route('tasks.index')
            ->with('success', 'Task created successfully.');
    }

    /**
     * Update the task status.
     */
    public function updateStatus(Request $request, Task $task): RedirectResponse
    {
        // Check authorization - user can only update their own tasks
        if ($task->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $task->update($validated);

        return redirect()->back()->with('success', 'Task status updated successfully.');
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(Task $task): RedirectResponse
    {
        // Check authorization - user can only delete their own tasks
        if ($task->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Task deleted successfully.');
    }
}
