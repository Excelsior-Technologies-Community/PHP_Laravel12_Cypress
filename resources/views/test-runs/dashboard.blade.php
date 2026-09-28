@extends('test-cases.layout')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h2 class="mb-1">
            Cypress Test Analytics
        </h2>

        <p class="text-muted mb-0">
            Test execution statistics and Cypress activity.
        </p>

    </div>

    <a
        href="{{ route('test-runs.index') }}"
        class="btn btn-dark"
    >
        Test Run History
    </a>

</div>


<div class="row g-4 mb-4">

    <div class="col-md-3">

        <div class="card shadow-sm border-0">

            <div class="card-body">

                <p class="text-muted mb-1">
                    Total Tests
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
                    Passed
                </p>

                <h2 class="text-success">
                    {{ $passed }}
                </h2>

            </div>

        </div>

    </div>


    <div class="col-md-3">

        <div class="card shadow-sm border-0">

            <div class="card-body">

                <p class="text-muted mb-1">
                    Failed
                </p>

                <h2 class="text-danger">
                    {{ $failed }}
                </h2>

            </div>

        </div>

    </div>


    <div class="col-md-3">

        <div class="card shadow-sm border-0">

            <div class="card-body">

                <p class="text-muted mb-1">
                    Pass Rate
                </p>

                <h2>
                    {{ $passRate }}%
                </h2>

            </div>

        </div>

    </div>

</div>


<div class="row g-4 mb-4">

    <div class="col-md-4">

        <div class="card shadow-sm">

            <div class="card-body">

                <h5>
                    Test Status Summary
                </h5>

                <hr>

                <p>
                    Passed:
                    <strong class="text-success">
                        {{ $passed }}
                    </strong>
                </p>

                <p>
                    Failed:
                    <strong class="text-danger">
                        {{ $failed }}
                    </strong>
                </p>

                <p>
                    Skipped:
                    <strong class="text-secondary">
                        {{ $skipped }}
                    </strong>
                </p>

                <p class="mb-0">
                    Average Duration:
                    <strong>
                        {{ $averageDuration
                            ? round($averageDuration, 2)
                            : 0
                        }} ms
                    </strong>
                </p>

            </div>

        </div>

    </div>


    <div class="col-md-4">

        <div class="card shadow-sm">

            <div class="card-body">

                <h5>
                    Duration Analytics
                </h5>

                <hr>

                <p>
                    Fastest:
                    <strong class="text-success">
                        {{ $minimumDuration ?? 0 }} ms
                    </strong>
                </p>

                <p>
                    Average:
                    <strong>
                        {{ $averageDuration
                            ? round($averageDuration, 2)
                            : 0
                        }} ms
                    </strong>
                </p>

                <p class="mb-0">
                    Slowest:
                    <strong class="text-danger">
                        {{ $maximumDuration ?? 0 }} ms
                    </strong>
                </p>

            </div>

        </div>

    </div>


    <div class="col-md-4">

        <div class="card shadow-sm">

            <div class="card-body">

                <h5>
                    Cypress Testing
                </h5>

                <hr>

                <p class="text-muted">
                    Automated Cypress E2E execution
                    results stored by Laravel.
                </p>

                <a
                    href="{{ route('test-cases.index') }}"
                    class="btn btn-primary"
                >
                    Manage Test Cases
                </a>

            </div>

        </div>

    </div>

</div>


<div class="row g-4 mb-4">

    <!-- Functionality 8 -->

    <div class="col-md-6">

        <div class="card shadow-sm">

            <div class="card-header bg-danger text-white">
                Top Failed Specifications
            </div>

            <div class="table-responsive">

                <table class="table table-hover mb-0">

                    <thead>

                        <tr>

                            <th>
                                Specification
                            </th>

                            <th>
                                Failures
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                    @forelse($topFailedSpecs as $spec)

                        <tr>

                            <td>
                                {{ $spec->spec_name }}
                            </td>

                            <td>

                                <span class="badge bg-danger">
                                    {{ $spec->failed_count }}
                                </span>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="2"
                                class="text-center text-muted py-3"
                            >
                                No failed specifications.
                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    <!-- Functionality 9 -->

    <div class="col-md-6">

        <div class="card shadow-sm">

            <div class="card-header bg-warning">
                Slowest Test Runs
            </div>

            <div class="table-responsive">

                <table class="table table-hover mb-0">

                    <thead>

                        <tr>

                            <th>
                                Test
                            </th>

                            <th>
                                Duration
                            </th>

                            <th>
                                Status
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                    @forelse($slowestRuns as $run)

                        <tr>

                            <td>
                                {{ $run->test_name ?: $run->spec_name }}
                            </td>

                            <td>
                                {{ $run->duration }} ms
                            </td>

                            <td>

                                @if($run->status === 'passed')

                                    <span class="badge bg-success">
                                        Passed
                                    </span>

                                @elseif($run->status === 'failed')

                                    <span class="badge bg-danger">
                                        Failed
                                    </span>

                                @else

                                    <span class="badge bg-secondary">
                                        Skipped
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="3"
                                class="text-center text-muted py-3"
                            >
                                No duration data available.
                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>


<div class="card shadow-sm">

    <div class="card-header bg-dark text-white">
        Recent Test Runs
    </div>

    <div class="table-responsive">

        <table class="table table-hover mb-0">

            <thead>

                <tr>

                    <th>Spec</th>
                    <th>Test</th>
                    <th>Status</th>
                    <th>Browser</th>
                    <th>Duration</th>
                    <th>Executed</th>

                </tr>

            </thead>

            <tbody>

            @forelse($recentRuns as $run)

                <tr>

                    <td>
                        {{ $run->spec_name }}
                    </td>

                    <td>
                        {{ $run->test_name ?: '—' }}
                    </td>

                    <td>

                        @if($run->status === 'passed')

                            <span class="badge bg-success">
                                Passed
                            </span>

                        @elseif($run->status === 'failed')

                            <span class="badge bg-danger">
                                Failed
                            </span>

                        @else

                            <span class="badge bg-secondary">
                                Skipped
                            </span>

                        @endif

                    </td>

                    <td>
                        {{ $run->browser ?: '—' }}
                    </td>

                    <td>
                        {{ $run->duration ?? 0 }} ms
                    </td>

                    <td>
                        {{ $run->executed_at?->format(
                            'd M Y H:i'
                        ) }}
                    </td>

                </tr>

            @empty

                <tr>

                    <td
                        colspan="6"
                        class="text-center text-muted py-4"
                    >
                        No Cypress test runs recorded yet.
                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection