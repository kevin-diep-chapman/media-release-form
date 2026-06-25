@extends('layouts.app')
@section('title', 'User Management')
@section('content')
    <nav class="dashboard-breadcrumbs" aria-label="Breadcrumb">
        <a href="{{ route('public.permitted-events') }}">Home</a>
        <span aria-hidden="true">&gt;</span>
        <span>User</span>
    </nav>

    <header class="dashboard-intro dashboard-intro-with-action">
        <div>
            <h1>User Management</h1>
            <p>Manage Chapman Media accounts.</p>
        </div>
        <button type="button" class="dashboard-new-event" onclick="openCreateUserModal()">+ New User</button>
    </header>

    @if (session('success') || request()->query('success'))
        <section class="card dashboard-alert dashboard-alert-success" id="userManagementSuccessAlert">
            <p>{{ session('success', request()->query('success')) }}</p>
        </section>
    @endif

    <section class="dashboard-table-card">
        @if ($users->isEmpty())
            <p class="dashboard-empty">No users found.</p>
        @else
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Title</th>
                        <th>Department</th>
                        <th>Role</th>
                        <th class="action-column">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->first_name ?: $user->name }}</td>
                            <td>{{ $user->last_name ?: '—' }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->title ?: '—' }}</td>
                            <td>{{ $user->department ?: '—' }}</td>
                            <td>{{ $user->role === 'admin' ? 'Admin' : 'User' }}</td>
                            <td class="action-column">
                                <div class="table-actions">
                                    @php
                                        $userData = [
                                            'id' => $user->id,
                                            'first_name' => $user->first_name,
                                            'last_name' => $user->last_name,
                                            'email' => $user->email,
                                            'title' => $user->title,
                                            'department' => $user->department,
                                            'role' => $user->role,
                                        ];
                                    @endphp
                                    <button
                                        type="button"
                                        class="button-edit user-edit-btn"
                                        data-user='@json($userData)'
                                    >Edit</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </section>

    @include('components.user-form-modal')
    <script src="{{ asset('js/user-management.js') }}"></script>

    @if (request()->query('success'))
        <script>
            if (window.history.replaceState) {
                const url = new URL(window.location.href);
                url.searchParams.delete('success');
                window.history.replaceState({}, '', url);
            }
        </script>
    @endif
@endsection
