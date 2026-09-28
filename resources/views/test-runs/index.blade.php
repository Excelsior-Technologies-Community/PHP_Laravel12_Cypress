@extends('test-cases.layout')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h2>
            Cypress Test Run History
        </h2>

        <p class="text-muted mb-0">
            Search, filter and manage previous test executions.
        </p>

    </div>

    <div class="d-flex gap-2">

        <a
            href="{{ route('test-runs.dashboard') }}"
            class="btn btn-dark"
        >
            Analytics
        </a>

        <a
            href="{{ route('test-runs.export', request()->query()) }}"
            class="btn btn-success"
        >
            Export CSV
        </a>

    </div>

</div>


<form
    method="GET"
    action="{{ route('test-runs.index') }}"
    class="card card-body mb-4"
>

    <div class="row g-3">

        <div class="col-md-4">

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


        <div class="col-md-2">

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


        <div class="col-md-2">

            <label class="form-label">
                From
            </label>

            <input
                type="date"
                name="date_from"
                class="form-control"
                value="{{ request('date_from') }}"
            >

        </div>


        <div class="col-md-2">

            <label class="form-label">
                To
            </label>

            <input
                type="date"
                name="date_to"
                class="form-control"
                value="{{ request('date_to') }}"
            >

        </div>


        <div class="col-md-2 d-flex align-items-end">

            <button class="btn btn-primary w-100">
                Filter
            </button>

        </div>

    </div>

</form>


<form
    method="POST"
    action="{{ route('test-runs.bulk-destroy') }}"
    id="bulkRunDeleteForm"
>

    @csrf
    @method('DELETE')


    <div class="card shadow-sm">

        <div class="card-header d-flex justify-content-between">

            <strong>
                Test Runs
            </strong>

            <button
                type="submit"
                class="btn btn-sm btn-danger"
                onclick="return confirm('Delete selected test runs?')"
            >
                Delete Selected
            </button>

        </div>


        <div class="table-responsive">

            <table class="table table-hover mb-0">

                <thead class="table-dark">

                    <tr>

                        <th>
                            <input
                                type="checkbox"
                                id="selectAllRuns"
                            >
                        </th>

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

                            <input
                                type="checkbox"
                                name="ids[]"
                                value="{{ $run->id }}"
                                class="run-checkbox"
                            >

                        </td>


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
                            {{ $run->executed_at?->format(
                                'd M Y H:i:s'
                            ) }}
                        </td>

                    </tr>


                    @if($run->error_message)

                        <tr>

                            <td></td>

                            <td
                                colspan="7"
                                class="text-danger small"
                            >

                                <strong>
                                    Failure:
                                </strong>

                                {{ $run->error_message }}

                            </td>

                        </tr>

                    @endif

                @empty

                    <tr>

                        <td
                            colspan="8"
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

</form>


{{-- Numeric Pagination Only --}}
@if($testRuns->hasPages())

    <div class="d-flex justify-content-center mt-4">

        <ul class="pagination">

            {{-- Page Numbers Only --}}
            @for($page = 1; $page <= $testRuns->lastPage(); $page++)

                <li
                    class="page-item {{ $page == $testRuns->currentPage() ? 'active' : '' }}"
                >

                    <a
                        class="page-link"
                        href="{{ $testRuns->appends(request()->query())->url($page) }}"
                    >
                        {{ $page }}
                    </a>

                </li>

            @endfor

        </ul>

    </div>

@endif


<script>

document.getElementById('selectAllRuns')
    ?.addEventListener('change', function () {

        document
            .querySelectorAll('.run-checkbox')
            .forEach(function (checkbox) {

                checkbox.checked = this.checked;

            }, this);

    });

</script>

@endsection