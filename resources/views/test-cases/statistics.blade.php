@extends('test-cases.layout')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h2>
            Test Case Statistics
        </h2>

        <p class="text-muted mb-0">
            Overview of your Cypress test case library.
        </p>

    </div>

    <a
        href="{{ route('test-cases.index') }}"
        class="btn btn-dark"
    >
        Back to Test Cases
    </a>

</div>


<div class="row g-4 mb-4">

    <div class="col-md-3">

        <div class="card shadow-sm border-0">

            <div class="card-body">

                <p class="text-muted mb-1">
                    Total Test Cases
                </p>

                <h2>
                    {{ $total }}
                </h2>

            </div>

        </div>

    </div>


    <div class="col-md-3">

        <div class="card shadow-sm border-0">

            <div class="card-body">

                <p class="text-muted mb-1">
                    Active
                </p>

                <h2 class="text-success">
                    {{ $active }}
                </h2>

            </div>

        </div>

    </div>


    <div class="col-md-3">

        <div class="card shadow-sm border-0">

            <div class="card-body">

                <p class="text-muted mb-1">
                    Inactive
                </p>

                <h2 class="text-secondary">
                    {{ $inactive }}
                </h2>

            </div>

        </div>

    </div>


    <div class="col-md-3">

        <div class="card shadow-sm border-0">

            <div class="card-body">

                <p class="text-muted mb-1">
                    High Priority
                </p>

                <h2 class="text-danger">
                    {{ $high }}
                </h2>

            </div>

        </div>

    </div>

</div>


<div class="row g-4 mb-4">

    <div class="col-md-6">

        <div class="card shadow-sm">

            <div class="card-body">

                <h5>
                    Priority Summary
                </h5>

                <hr>

                <p>
                    Low:
                    <strong class="text-success">
                        {{ $low }}
                    </strong>
                </p>

                <p>
                    Medium:
                    <strong class="text-warning">
                        {{ $medium }}
                    </strong>
                </p>

                <p class="mb-0">
                    High:
                    <strong class="text-danger">
                        {{ $high }}
                    </strong>
                </p>

            </div>

        </div>

    </div>


    <div class="col-md-6">

        <div class="card shadow-sm">

            <div class="card-body">

                <h5>
                    Test Cases by Module
                </h5>

                <hr>

                @forelse($modules as $module)

                    <div
                        class="d-flex justify-content-between border-bottom py-2"
                    >

                        <span>
                            {{ $module->module }}
                        </span>

                        <strong>
                            {{ $module->total }}
                        </strong>

                    </div>

                @empty

                    <p class="text-muted">
                        No module data available.
                    </p>

                @endforelse

            </div>

        </div>

    </div>

</div>


<div class="card shadow-sm">

    <div class="card-header bg-dark text-white">
        Recent Test Cases
    </div>

    <div class="table-responsive">

        <table class="table table-hover mb-0">

            <thead>

                <tr>

                    <th>#</th>
                    <th>Title</th>
                    <th>Module</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Created</th>

                </tr>

            </thead>

            <tbody>

            @forelse($recentCases as $case)

                <tr>

                    <td>
                        {{ $case->id }}
                    </td>

                    <td>
                        {{ $case->title }}
                    </td>

                    <td>
                        {{ $case->module ?: '—' }}
                    </td>

                    <td>
                        {{ ucfirst($case->priority) }}
                    </td>

                    <td>
                        {{ ucfirst($case->status) }}
                    </td>

                    <td>
                        {{ $case->created_at->format('d M Y') }}
                    </td>

                </tr>

            @empty

                <tr>

                    <td
                        colspan="6"
                        class="text-center text-muted py-4"
                    >
                        No test cases available.
                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection