@extends('test-cases.layout')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h2>
            Cypress Test Run History
        </h2>

        <p class="text-muted mb-0">
            Search and filter previous automated test executions.
        </p>

    </div>

    <a
        href="{{ route('test-runs.dashboard') }}"
        class="btn btn-dark"
    >
        Analytics
    </a>

</div>

<form
    method="GET"
    action="{{ route('test-runs.index') }}"
    class="card card-body mb-4"
>

    <div class="row g-3">

        <div class="col-md-7">

            <label class="form-label">
                Search
            </label>

            <input
                type="text"
                name="search"
                class="form-control"
                value="{{ request('search') }}"
                placeholder="Search spec or test name..."
            >

        </div>

        <div class="col-md-3">

            <label class="form-label">
                Status
            </label>

            <select
                name="status"
                class="form-select"
            >

                <option value="">
                    All
                </option>

                <option
                    value="passed"
                    @selected(request('status') === 'passed')
                >
                    Passed
                </option>

                <option
                    value="failed"
                    @selected(request('status') === 'failed')
                >
                    Failed
                </option>

                <option
                    value="skipped"
                    @selected(request('status') === 'skipped')
                >
                    Skipped
                </option>

            </select>

        </div>

        <div class="col-md-2 d-flex align-items-end">

            <button class="btn btn-primary w-100">
                Filter
            </button>

        </div>

    </div>

</form>

<div class="card shadow-sm">

    <div class="table-responsive">

        <table class="table table-hover mb-0">

            <thead class="table-dark">

                <tr>
                    <th>#</th>
                    <th>Spec</th>
                    <th>Test</th>
                    <th>Status</th>
                    <th>Browser</th>
                    <th>Duration</th>
                    <th>Executed</th>
                </tr>

            </thead>

            <tbody>

            @forelse($testRuns as $run)

                <tr>

                    <td>
                        {{ $testRuns->firstItem() + $loop->index }}
                    </td>

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
                        {{ $run->executed_at?->format('d M Y H:i:s') }}
                    </td>

                </tr>

                @if($run->error_message)

                    <tr>

                        <td></td>

                        <td
                            colspan="6"
                            class="text-danger small"
                        >
                            <strong>Failure:</strong>
                            {{ $run->error_message }}
                        </td>

                    </tr>

                @endif

            @empty

                <tr>

                    <td
                        colspan="7"
                        class="text-center py-4 text-muted"
                    >
                        No test runs found.
                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>

</div>

<div class="mt-3">
    {{ $testRuns->links() }}
</div>

@endsection