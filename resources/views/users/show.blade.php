@extends('layouts.app')

@php
    use Illuminate\Support\Str;
@endphp

@section('title', $user->name)
@section('page-title', $user->name)

@section('header-actions')
    <div class="d-flex gap-2">
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Users
        </a>
        <a href="{{ route('tasks.create', ['user_id' => $user->id]) }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Add Task
        </a>
    </div>
@endsection

@section('content')
<!-- User Info Card -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="user-avatar" style="width: 80px; height: 80px; font-size: 2rem;">
                        {{ Str::substr($user->name, 0, 1) }}
                    </div>
                    <div class="ms-4 flex-grow-1">
                        <h3 class="mb-2">{{ $user->name }}</h3>
                        <p class="text-muted mb-0">
                            <i class="bi bi-envelope me-2"></i>{{ $user->email }}
                        </p>
                    </div>
                    <div class="text-end">
                        <div class="stats-number">{{ $user->tasks->count() }}</div>
                        <div class="stats-label">Total Tasks</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tasks Section -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-list-task me-2"></i>Tasks</span>
        <span class="badge bg-light text-dark">{{ $user->tasks->count() }} tasks</span>
    </div>
    <div class="card-body p-0">
        @if($user->tasks->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($user->tasks as $task)
                            <tr>
                                <td>
                                    <strong>{{ $task->title }}</strong>
                                </td>
                                <td>
                                    <span class="text-muted">{{ Str::limit($task->description ?? 'No description', 50) }}</span>
                                </td>
                                <td>
                                    <form action="{{ route('tasks.updateStatus', $task) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" 
                                                class="form-select form-select-sm status-select" 
                                                style="width: auto; min-width: 140px;"
                                                dusk="status-select"
                                                onchange="this.form.submit()">
                                            <option value="pending" {{ $task->status === 'pending' ? 'selected' : '' }}>
                                                ⏳ Pending
                                            </option>
                                            <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>
                                                🔄 In Progress
                                            </option>
                                            <option value="completed" {{ $task->status === 'completed' ? 'selected' : '' }}>
                                                ✅ Completed
                                            </option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar3 me-1"></i>{{ $task->created_at->format('M d, Y') }}
                                    </small>
                                </td>
                                <td class="text-end">
                                    <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Are you sure you want to delete this task?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="card-body">
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="bi bi-inbox"></i>
                    </div>
                    <h4>No Tasks Found</h4>
                    <p class="mb-4">This user doesn't have any tasks yet.</p>
                    <a href="{{ route('tasks.create', ['user_id' => $user->id]) }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Create First Task
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
