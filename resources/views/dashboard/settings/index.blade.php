@extends('layouts.app')
@section('title', 'Settings')
@section('content')
    <nav class="dashboard-breadcrumbs" aria-label="Breadcrumb">
        <a href="{{ route('public.permitted-events') }}">Home</a>
        <span aria-hidden="true">&gt;</span>
        <span>Settings</span>
    </nav>

    <header class="dashboard-intro">
        <div>
            <h1>Settings</h1>
            <p>Manage the AI chatbox and knowledge base for Chapman Media.</p>
        </div>
    </header>

    @if (session('success'))
        <section class="card dashboard-alert dashboard-alert-success">
            <p>{{ session('success') }}</p>
        </section>
    @endif

    @if ($errors->any())
        <section class="card dashboard-alert dashboard-alert-error">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </section>
    @endif

    <section class="card settings-card">
        <h2>AI Chatbox</h2>
        <p class="settings-description">
            When enabled, authenticated users see a floating assistant that answers questions using data from events, media releases, and users in the database.
        </p>

        <form method="POST" action="{{ route('dashboard.settings.update') }}" class="settings-form">
            @csrf
            @method('PUT')

            <label class="settings-toggle">
                <input
                    type="checkbox"
                    name="chatbox_enabled"
                    value="1"
                    @checked($chatboxEnabled)
                >
                <span class="settings-toggle-track" aria-hidden="true"></span>
                <span class="settings-toggle-label">Enable AI chatbox</span>
            </label>

            <div class="settings-actions">
                <button type="submit" class="dashboard-new-event">Save settings</button>
            </div>
        </form>
    </section>

    <section class="card settings-card">
        <h2>Knowledge Base</h2>
        <p class="settings-description">
            Rebuild embeddings from all database records. Run this after bulk imports or when search results seem outdated.
        </p>

        <dl class="settings-status-list">
            <div>
                <dt>OpenAI API key</dt>
                <dd>{{ $openAiConfigured ? 'Configured' : 'Not configured' }}</dd>
            </div>
            <div>
                <dt>Indexed chunks</dt>
                <dd>{{ number_format($chunkCount) }}</dd>
            </div>
            <div>
                <dt>Last reindex</dt>
                <dd>{{ $lastReindexAt?->timezone('America/Los_Angeles')->format('M j, Y g:i A T') ?? 'Never' }}</dd>
            </div>
            <div>
                <dt>Latest chunk indexed</dt>
                <dd>{{ $lastIndexedAt?->timezone('America/Los_Angeles')->format('M j, Y g:i A T') ?? 'Never' }}</dd>
            </div>
        </dl>

        <form method="POST" action="{{ route('dashboard.settings.reindex') }}" class="settings-actions">
            @csrf
            <button type="submit" class="button-link-secondary" @disabled(! $openAiConfigured)>Reindex knowledge base</button>
        </form>
    </section>
@endsection
