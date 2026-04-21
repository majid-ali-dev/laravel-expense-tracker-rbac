@extends('layouts.app')

@section('title', 'Manager Dashboard')
@section('page_title', 'Manager Dashboard')
@section('page_subtitle', 'Coordinate users and keep team operations organized from one place.')

@section('content')
<div class="d-flex flex-column gap-4">
    <div>
        <span class="page-eyebrow"><i class="bi bi-briefcase-fill"></i> Workspace</span>
        <h1 class="page-title mt-3">Manage people and processes with a clearer dashboard.</h1>
        <p class="page-description">This view is designed to help managers move through account oversight and operational tasks with less visual clutter.</p>
    </div>

    <div class="row g-4">
        <div class="col-md-6 col-xl-4">
            <div class="metric-card">
                <span class="metric-icon"><i class="bi bi-people-fill"></i></span>
                <div class="metric-value">Users</div>
                <div class="metric-label">Management</div>
                <p class="section-text mt-2">A clean space for team and account oversight.</p>
            </div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div class="metric-card">
                <span class="metric-icon"><i class="bi bi-kanban-fill"></i></span>
                <div class="metric-value">Flow</div>
                <div class="metric-label">Operations</div>
                <p class="section-text mt-2">Move between responsibilities in a more structured layout.</p>
            </div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div class="metric-card">
                <span class="metric-icon"><i class="bi bi-bar-chart-line-fill"></i></span>
                <div class="metric-value">Ready</div>
                <div class="metric-label">Insights</div>
                <p class="section-text mt-2">Prepared for future summaries and activity widgets.</p>
            </div>
        </div>
    </div>

    <div class="section-card">
        <h2 class="section-title mb-3">Manager Focus</h2>
        <p class="section-text mb-0">The layout now emphasizes navigation, readability, and responsive behavior while keeping all role-based menu logic and functionality exactly the same.</p>
    </div>
</div>
@endsection
