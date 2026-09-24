@extends('test-cases.layout')

@section('content')

<div class="d-flex justify-content-between mb-4">

    <h2>
        Test Case Details
    </h2>

    <a
        href="{{ route('test-cases.index') }}"
        class="btn btn-secondary"
    >
        Back
    </a>

</div>

<div class="card shadow-sm">

    <div class="card-body">

        <h4>
            {{ $testCase->title }}
        </h4>

        <hr>

        <p>
            <strong>Module:</strong>
            {{ $testCase->module ?: '—' }}
        </p>

        <p>
            <strong>Priority:</strong>
            {{ ucfirst($testCase->priority) }}
        </p>

        <p>
            <strong>Status:</strong>
            {{ ucfirst($testCase->status) }}
        </p>

        <p>
            <strong>Description:</strong>
        </p>

        <p class="text-muted">
            {{ $testCase->description ?: 'No description provided.' }}
        </p>

        <p>
            <strong>Created:</strong>
            {{ $testCase->created_at->format('d M Y H:i') }}
        </p>

    </div>

</div>

@endsection