@extends('test-cases.layout')

@section('content')

<h2 class="mb-4">
    Create Cypress Test Case
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
            action="{{ route('test-cases.store') }}"
        >

            @csrf

            <div class="mb-3">

                <label class="form-label">
                    Test Case Title
                </label>

                <input
                    type="text"
                    name="title"
                    class="form-control"
                    value="{{ old('title') }}"
                    placeholder="User can login successfully"
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
                    value="{{ old('module') }}"
                    placeholder="Authentication"
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
                    placeholder="Describe the E2E scenario..."
                >{{ old('description') }}</textarea>

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

                        <option value="low">
                            Low
                        </option>

                        <option
                            value="medium"
                            selected
                        >
                            Medium
                        </option>

                        <option value="high">
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
                            selected
                        >
                            Active
                        </option>

                        <option value="inactive">
                            Inactive
                        </option>

                    </select>

                </div>

            </div>

            <button class="btn btn-primary">
                Create Test Case
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