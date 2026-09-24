@extends('test-cases.layout')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h2 class="mb-1">Cypress Test Cases</h2>
        <p class="text-muted mb-0">
            Manage automated E2E test cases.
        </p>
    </div>

    <a
        href="{{ route('test-cases.create') }}"
        class="btn btn-primary"
    >
        + Add Test Case
    </a>

</div>

<form
    method="GET"
    action="{{ route('test-cases.index') }}"
    class="card card-body mb-4"
>

    <div class="row g-3">

        <div class="col-md-5">

            <label class="form-label">
                Search
            </label>

            <input
                type="text"
                name="search"
                class="form-control"
                value="{{ request('search') }}"
                placeholder="Search title, module..."
            >

        </div>

        <div class="col-md-3">

            <label class="form-label">
                Priority
            </label>

            <select
                name="priority"
                class="form-select"
            >

                <option value="">
                    All Priorities
                </option>

                <option
                    value="low"
                    @selected(request('priority') === 'low')
                >
                    Low
                </option>

                <option
                    value="medium"
                    @selected(request('priority') === 'medium')
                >
                    Medium
                </option>

                <option
                    value="high"
                    @selected(request('priority') === 'high')
                >
                    High
                </option>

            </select>

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
                    All Status
                </option>

                <option
                    value="active"
                    @selected(request('status') === 'active')
                >
                    Active
                </option>

                <option
                    value="inactive"
                    @selected(request('status') === 'inactive')
                >
                    Inactive
                </option>

            </select>

        </div>

        <div class="col-md-1 d-flex align-items-end">

            <button class="btn btn-dark w-100">
                Go
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
                    <th>Title</th>
                    <th>Module</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>

            </thead>

            <tbody>

            @forelse($testCases as $testCase)

                <tr>

                    <td>
                        {{ $testCases->firstItem() + $loop->index }}
                    </td>

                    <td>
                        <strong>
                            {{ $testCase->title }}
                        </strong>
                    </td>

                    <td>
                        {{ $testCase->module ?: '—' }}
                    </td>

                    <td>

                        @if($testCase->priority === 'high')
                            <span class="badge bg-danger">
                                High
                            </span>
                        @elseif($testCase->priority === 'medium')
                            <span class="badge bg-warning text-dark">
                                Medium
                            </span>
                        @else
                            <span class="badge bg-success">
                                Low
                            </span>
                        @endif

                    </td>

                    <td>

                        @if($testCase->status === 'active')
                            <span class="badge bg-success">
                                Active
                            </span>
                        @else
                            <span class="badge bg-secondary">
                                Inactive
                            </span>
                        @endif

                    </td>

                    <td>
                        {{ $testCase->created_at->format('d M Y') }}
                    </td>

                    <td>

                        <a
                            href="{{ route('test-cases.show', $testCase) }}"
                            class="btn btn-sm btn-info"
                        >
                            View
                        </a>

                        <a
                            href="{{ route('test-cases.edit', $testCase) }}"
                            class="btn btn-sm btn-primary"
                        >
                            Edit
                        </a>

                        <form
                            action="{{ route('test-cases.destroy', $testCase) }}"
                            method="POST"
                            class="d-inline"
                        >

                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="btn btn-sm btn-danger"
                                onclick="return confirm('Delete this test case?')"
                            >
                                Delete
                            </button>

                        </form>

                    </td>

                </tr>

            @empty

                <tr>
                    <td
                        colspan="7"
                        class="text-center py-4 text-muted"
                    >
                        No test cases found.
                    </td>
                </tr>

            @endforelse

            </tbody>

        </table>

    </div>

</div>

<div class="mt-3">
    {{ $testCases->links() }}
</div>

@endsection