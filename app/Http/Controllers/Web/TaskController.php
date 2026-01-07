<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
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
        $user = Auth::user();
        
        // Admins can see all tasks, regular users only see their own
        if ($user->isAdmin()) {
            $tasks = Task::with(['user', 'creator'])->latest()->get();
        } else {
            $tasks = Task::where('user_id', $user->id)
                ->with(['user', 'creator'])
                ->latest()
                ->get();
        }

        return view('tasks.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new task.
     */
    public function create(): View
    {
        $users = null;
        
        // Only admins can assign tasks to other users
        if (Auth::user()->isAdmin()) {
            $users = User::orderBy('name')->get();
        }
        
        return view('tasks.create', compact('users'));
    }

    /**
     * Store a newly created task in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $assignToUserId = $request->input('user_id');
        
        // Admins can assign to any user, regular users can only assign to themselves
        if ($user->isAdmin() && $assignToUserId) {
            $targetUserId = $assignToUserId;
            // Validate that the user exists
            $targetUser = User::findOrFail($targetUserId);
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

        Task::create($validated);

        $message = $user->isAdmin() && $assignToUserId && $assignToUserId != $user->id
            ? 'Task assigned successfully.'
            : 'Task created successfully.';

        return redirect()->route('tasks.index')
            ->with('success', $message);
    }

    /**
     * Update the task status.
     */
    public function updateStatus(Request $request, Task $task): RedirectResponse
    {
        $user = Auth::user();
        
        // Admins can update any task, regular users can only update their own
        if (!$user->isAdmin() && $task->user_id !== $user->id) {
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
        $user = Auth::user();
        
        // Admins can delete any task, regular users can only delete their own
        if (!$user->isAdmin() && $task->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Task deleted successfully.');
    }
}
