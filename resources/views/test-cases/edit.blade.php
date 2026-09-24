@extends('test-cases.layout')

@section('content')

<h2 class="mb-4">
    Edit Cypress Test Case
</h2>

<div class="card shadow-sm">

    <div class="card-body">

        @if($errors->any())

            <div class="alert alert-danger">

                <ul class="mb-0">

                    @foreach($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        @endif

        <form
            method="POST"
            action="{{ route('test-cases.update', $testCase) }}"
        >

            @csrf
            @method('PUT')

            <div class="mb-3">

                <label class="form-label">
                    Test Case Title
                </label>

                <input
                    type="text"
                    name="title"
                    class="form-control"
                    value="{{ old('title', $testCase->title) }}"
                    required
                >

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Module
                </label>

                <input
                    type="text"
                    name="module"
                    class="form-control"
                    value="{{ old('module', $testCase->module) }}"
                >

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Description
                </label>

                <textarea
                    name="description"
                    class="form-control"
                    rows="4"
                >{{ old('description', $testCase->description) }}</textarea>

            </div>

            <div class="row">

                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Priority
                    </label>

                    <select
                        name="priority"
                        class="form-select"
                    >

                        <option
                            value="low"
                            @selected($testCase->priority === 'low')
                        >
                            Low
                        </option>

                        <option
                            value="medium"
                            @selected($testCase->priority === 'medium')
                        >
                            Medium
                        </option>

                        <option
                            value="high"
                            @selected($testCase->priority === 'high')
                        >
                            High
                        </option>

                    </select>

                </div>

                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Status
                    </label>

                    <select
                        name="status"
                        class="form-select"
                    >

                        <option
                            value="active"
                            @selected($testCase->status === 'active')
                        >
                            Active
                        </option>

                        <option
                            value="inactive"
                            @selected($testCase->status === 'inactive')
                        >
                            Inactive
                        </option>

                    </select>

                </div>

            </div>

            <button class="btn btn-primary">
                Update Test Case
            </button>

            <a
                href="{{ route('test-cases.index') }}"
                class="btn btn-secondary"
            >
                Cancel
            </a>

        </form>

    </div>

</div>

@endsection