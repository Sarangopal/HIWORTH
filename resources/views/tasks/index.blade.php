@extends('layouts.app')

@php
    use Illuminate\Support\Str;
@endphp

@section('title', 'Tasks')
@section('page-title', 'Tasks Management')

@section('header-actions')
    <a href="{{ route('tasks.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Create Task
    </a>
@endsection

@section('content')
<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="stats-card">
            <div class="stats-label">Total Tasks</div>
            <div class="stats-number">{{ $tasks->count() }}</div>
            <i class="bi bi-list-task text-primary" style="font-size: 2rem; opacity: 0.3;"></i>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stats-card">
            <div class="stats-label">Pending</div>
            <div class="stats-number">{{ $tasks->where('status', 'pending')->count() }}</div>
            <i class="bi bi-clock text-warning" style="font-size: 2rem; opacity: 0.3;"></i>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stats-card">
            <div class="stats-label">In Progress</div>
            <div class="stats-number">{{ $tasks->where('status', 'in_progress')->count() }}</div>
            <i class="bi bi-arrow-repeat text-info" style="font-size: 2rem; opacity: 0.3;"></i>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stats-card">
            <div class="stats-label">Completed</div>
            <div class="stats-number">{{ $tasks->where('status', 'completed')->count() }}</div>
            <i class="bi bi-check-circle text-success" style="font-size: 2rem; opacity: 0.3;"></i>
        </div>
    </div>
</div>

<!-- Tasks Table -->
@if($tasks->count() > 0)
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-list-task me-2"></i>All Tasks</span>
            <span class="badge bg-light text-dark">{{ $tasks->count() }} tasks</span>
        </div>
        <div class="card-body p-0">
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
                        @foreach($tasks as $task)
                            <tr>
                                <td>
                                    <strong>{{ $task->title }}</strong>
                                </td>
                                <td>
                                    <span class="text-muted">{{ Str::limit($task->description ?? 'No description', 40) }}</span>
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
        </div>
    </div>
@else
    <div class="card">
        <div class="card-body">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="bi bi-inbox"></i>
                </div>
                <h4>No Tasks Found</h4>
                <p class="mb-4">Get started by creating your first task.</p>
                <a href="{{ route('tasks.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Create Task
                </a>
            </div>
        </div>
    </div>
@endif
@endsection
