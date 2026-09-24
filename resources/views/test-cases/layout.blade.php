<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>Cypress Test Management</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <a
            href="{{ route('dashboard') }}"
            class="navbar-brand"
        >
            Cypress Testing
        </a>

        <div>
            <a
                href="{{ route('dashboard') }}"
                class="btn btn-outline-light btn-sm"
            >
                Dashboard
            </a>

            <a
                href="{{ route('test-cases.index') }}"
                class="btn btn-outline-light btn-sm"
            >
                Test Cases
            </a>

            <a
                href="{{ route('test-runs.index') }}"
                class="btn btn-outline-light btn-sm"
            >
                Test Runs
            </a>

            <a
                href="{{ route('test-runs.dashboard') }}"
                class="btn btn-outline-light btn-sm"
            >
                Analytics
            </a>
        </div>
    </div>
</nav>

<div class="container py-4">

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @yield('content')

</div>

</body>

</html>