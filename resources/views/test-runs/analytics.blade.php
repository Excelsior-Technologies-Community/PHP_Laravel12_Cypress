@extends('test-cases.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark">📊 Test Suite Analytics & Flaky Test Tracker</h2>
        <p class="text-muted mb-0">Deep performance analytics, execution duration breakdown, and automated flaky test detection.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('test-runs.dashboard') }}" class="btn btn-outline-secondary">
            ← Cypress Dashboard
        </a>
        <a href="{{ route('test-runs.index') }}" class="btn btn-dark">
            View All Test Runs
        </a>
    </div>
</div>

<!-- Summary Metric Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm border-0 border-start border-primary border-4">
            <div class="card-body">
                <p class="text-muted mb-1 small text-uppercase fw-bold">Total Executions</p>
                <h2 class="fw-bold mb-0 text-dark">{{ number_format($totalRuns) }}</h2>
                <small class="text-muted">Recorded runs</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 border-start border-success border-4">
            <div class="card-body">
                <p class="text-muted mb-1 small text-uppercase fw-bold">Pass Rate</p>
                <h2 class="fw-bold mb-0 text-success">{{ $passRate }}%</h2>
                <small class="text-muted">{{ number_format($passedRuns) }} passed</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 border-start border-danger border-4">
            <div class="card-body">
                <p class="text-muted mb-1 small text-uppercase fw-bold">Fail Rate</p>
                <h2 class="fw-bold mb-0 text-danger">{{ $failRate }}%</h2>
                <small class="text-muted">{{ number_format($failedRuns) }} failed</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 border-start border-warning border-4">
            <div class="card-body">
                <p class="text-muted mb-1 small text-uppercase fw-bold">Flaky Specs Detected</p>
                <h2 class="fw-bold mb-0 text-warning">{{ $flakySpecs->count() }}</h2>
                <small class="text-muted">Mixed Pass/Fail specs</small>
            </div>
        </div>
    </div>
</div>

<!-- Test Suite Health Bar -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-dark text-white fw-bold">
        📈 Overall Test Suite Health Ratios
    </div>
    <div class="card-body">
        <div class="d-flex justify-content-between mb-2 small fw-bold">
            <span class="text-success">Passed: {{ $passRate }}% ({{ number_format($passedRuns) }})</span>
            <span class="text-danger">Failed: {{ $failRate }}% ({{ number_format($failedRuns) }})</span>
            <span class="text-secondary">Skipped: {{ $skipRate }}% ({{ number_format($skippedRuns) }})</span>
        </div>
        <div class="progress" style="height: 22px;">
            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $passRate }}%" title="Passed: {{ $passRate }}%">{{ $passRate }}%</div>
            <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $failRate }}%" title="Failed: {{ $failRate }}%">{{ $failRate }}%</div>
            <div class="progress-bar bg-secondary" role="progressbar" style="width: {{ $skipRate }}%" title="Skipped: {{ $skipRate }}%">{{ $skipRate }}%</div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Flaky Test Tracker Panel -->
    <div class="col-lg-7">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-warning text-dark fw-bold d-flex justify-content-between align-items-center">
                <span>⚠️ Flaky Test Tracker (Inconsistent Specs)</span>
                <span class="badge bg-dark text-white">{{ $flakySpecs->count() }} Flagged</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Spec File Name</th>
                                <th class="text-center">Passed</th>
                                <th class="text-center">Failed</th>
                                <th class="text-center">Flakiness Index</th>
                                <th class="text-end">Avg Duration</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($flakySpecs as $flaky)
                                @php
                                    $flakyRate = round(($flaky->fail_count / $flaky->total_count) * 100, 1);
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark font-monospace small">{{ $flaky->spec_name }}</div>
                                        <small class="text-muted">{{ $flaky->total_count }} total executions</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success">{{ $flaky->pass_count }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-danger">{{ $flaky->fail_count }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-warning text-dark fw-bold">⚠️ {{ $flakyRate }}% Flaky</span>
                                    </td>
                                    <td class="text-end font-monospace small">
                                        {{ number_format($flaky->avg_duration) }} ms
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        🎉 No flaky tests detected! All specs exhibit stable pass/fail results.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Execution Duration & Browser Breakdown -->
    <div class="col-lg-5">
        <!-- Duration Benchmarks -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-dark text-white fw-bold">
                ⏱️ Execution Duration Benchmarks
            </div>
            <div class="card-body">
                <div class="row text-center mb-3">
                    <div class="col-4 border-end">
                        <div class="text-muted small">Min</div>
                        <div class="fw-bold text-success">{{ number_format($minDuration) }} ms</div>
                    </div>
                    <div class="col-4 border-end">
                        <div class="text-muted small">Average</div>
                        <div class="fw-bold text-primary">{{ number_format($avgDuration) }} ms</div>
                    </div>
                    <div class="col-4">
                        <div class="text-muted small">Max</div>
                        <div class="fw-bold text-danger">{{ number_format($maxDuration) }} ms</div>
                    </div>
                </div>

                <h6 class="fw-bold text-dark mb-2">Slowest Specs (Average Duration):</h6>
                <div class="list-group list-group-flush">
                    @forelse($specDurations as $spec)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                            <span class="font-monospace small text-truncate" style="max-width: 220px;" title="{{ $spec->spec_name }}">
                                {{ $spec->spec_name }}
                            </span>
                            <span class="badge bg-dark font-monospace">
                                {{ number_format($spec->avg_dur) }} ms
                            </span>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">No duration data available.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Browser Breakdown -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white fw-bold">
                🌐 Browser Execution Distribution
            </div>
            <div class="card-body">
                @forelse($browserStats as $b)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold text-capitalize">{{ $b->browser ?: 'Electron (Default)' }}</span>
                        <span class="badge bg-secondary font-monospace">{{ number_format($b->count) }} runs</span>
                    </div>
                    <div class="progress mb-3" style="height: 8px;">
                        @php
                            $bPct = $totalRuns > 0 ? round(($b->count / $totalRuns) * 100, 1) : 0;
                        @endphp
                        <div class="progress-bar bg-info" style="width: {{ $bPct }}%"></div>
                    </div>
                @empty
                    <p class="text-muted small mb-0">No browser breakdown available.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
