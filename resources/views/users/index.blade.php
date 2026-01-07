@extends('layouts.app')

@section('title', 'Users')
@section('page-title', 'Users Management')

@section('header-actions')
    <a href="{{ route('users.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Create User
    </a>
@endsection

@section('content')
@php
    use Illuminate\Support\Str;
@endphp

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="stats-card">
            <div class="stats-label">Total Users</div>
            <div class="stats-number">{{ $users->count() }}</div>
            <i class="bi bi-people text-primary" style="font-size: 2rem; opacity: 0.3;"></i>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="stats-card">
            <div class="stats-label">Total Tasks</div>
            <div class="stats-number">{{ $users->sum('tasks_count') }}</div>
            <i class="bi bi-list-task text-info" style="font-size: 2rem; opacity: 0.3;"></i>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="stats-card">
            <div class="stats-label">Active Users</div>
            <div class="stats-number">{{ $users->where('tasks_count', '>', 0)->count() }}</div>
            <i class="bi bi-person-check text-success" style="font-size: 2rem; opacity: 0.3;"></i>
        </div>
    </div>
</div>

<!-- Users Grid -->
@if($users->count() > 0)
    <div class="row">
        @foreach($users as $user)
            <div class="col-md-6 col-lg-4 mb-4 fade-in">
                <div class="card user-card">
                    <div class="card-body">
                        <div class="d-flex align-items-start mb-3">
                            <div class="user-avatar">
                                {{ Str::substr($user->name, 0, 1) }}
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <h5 class="user-name mb-1">{{ $user->name }}</h5>
                                <p class="user-email mb-0">
                                    <i class="bi bi-envelope me-1"></i>{{ $user->email }}
                                </p>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                @if($user->isAdmin())
                                <span class="badge bg-warning text-dark me-2">
                                    <i class="bi bi-shield-check me-1"></i>Admin
                                </span>
                                @endif
                                <span class="badge bg-primary">
                                    <i class="bi bi-list-task me-1"></i>{{ $user->tasks_count }} Tasks
                                </span>
                            </div>
                            <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-primary" dusk="user-{{ $user->id }}">
                                View Details <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="card">
        <div class="card-body">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="bi bi-people"></i>
                </div>
                <h4>No Users Found</h4>
                <p class="mb-4">Get started by creating your first user.</p>
                <a href="{{ route('users.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Create User
                </a>
            </div>
        </div>
    </div>
@endif
@endsection
