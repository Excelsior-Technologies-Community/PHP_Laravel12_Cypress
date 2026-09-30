<?php

namespace App\Http\Controllers;

use App\Models\TestRun;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class TestRunController extends Controller
{
    /**
     * Test run dashboard.
     */
    public function dashboard(): View
    {
        $total = TestRun::count();

        $passed = TestRun::where('status', 'passed')->count();

        $failed = TestRun::where('status', 'failed')->count();

        $skipped = TestRun::where('status', 'skipped')->count();

        $passRate = $total > 0
            ? round(($passed / $total) * 100, 2)
            : 0;

        $averageDuration = TestRun::whereNotNull('duration')
            ->avg('duration');

        $maximumDuration = TestRun::whereNotNull('duration')
            ->max('duration');

        $minimumDuration = TestRun::whereNotNull('duration')
            ->min('duration');

        $recentRuns = TestRun::latest('executed_at')
            ->latest()
            ->limit(10)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Functionality 8 - Top Failed Specifications
        |--------------------------------------------------------------------------
        */

        $topFailedSpecs = TestRun::selectRaw(
            'spec_name, COUNT(*) as failed_count'
        )
            ->where('status', 'failed')
            ->groupBy('spec_name')
            ->orderByDesc('failed_count')
            ->limit(10)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Functionality 9 - Slowest Test Runs
        |--------------------------------------------------------------------------
        */

        $slowestRuns = TestRun::whereNotNull('duration')
            ->orderByDesc('duration')
            ->limit(10)
            ->get();

        return view(
            'test-runs.dashboard',
            compact(
                'total',
                'passed',
                'failed',
                'skipped',
                'passRate',
                'averageDuration',
                'maximumDuration',
                'minimumDuration',
                'recentRuns',
                'topFailedSpecs',
                'slowestRuns'
            )
        );
    }

    /**
     * Test run history.
     */
    public function index(Request $request): View
    {
        $query = TestRun::query();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('spec_name', 'like', "%{$search}%")
                    ->orWhere('test_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        /*
        |--------------------------------------------------------------------------
        | Functionality 7 - Date Range Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('date_from')) {
            $query->whereDate(
                'executed_at',
                '>=',
                $request->date_from
            );
        }

        if ($request->filled('date_to')) {
            $query->whereDate(
                'executed_at',
                '<=',
                $request->date_to
            );
        }

        $testRuns = $query
            ->latest('executed_at')
            ->paginate(15)
            ->withQueryString();

        return view(
            'test-runs.index',
            compact('testRuns')
        );
    }

    /**
     * Store a Cypress test result.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'spec_name' => [
                'required',
                'string',
                'max:255',
            ],
            'test_name' => [
                'nullable',
                'string',
                'max:255',
            ],
            'status' => [
                'required',
                'in:passed,failed,skipped',
            ],
            'browser' => [
                'nullable',
                'string',
                'max:100',
            ],
            'duration' => [
                'nullable',
                'integer',
                'min:0',
            ],
            'error_message' => [
                'nullable',
                'string',
            ],
            'executed_at' => [
                'nullable',
                'date',
            ],
        ]);

        $testRun = TestRun::create([
            ...$validated,
            'executed_at' => $validated['executed_at'] ?? now(),
        ]);

        return response()->json([
            'message' => 'Test result stored successfully.',
            'data' => $testRun,
        ], 201);
    }

    /**
     * Functionality 5 - Bulk delete test runs.
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => [
                'required',
                'array',
                'min:1',
            ],
            'ids.*' => [
                'integer',
                'exists:test_runs,id',
            ],
        ]);

        $count = TestRun::whereIn(
            'id',
            $validated['ids']
        )->delete();

        return redirect()
            ->route('test-runs.index')
            ->with(
                'success',
                "{$count} test run(s) deleted successfully."
            );
    }

    /**
     * Functionality 6 - Export test runs to CSV.
     */
    public function export(Request $request): Response
    {
        $query = TestRun::query();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('spec_name', 'like', "%{$search}%")
                    ->orWhere('test_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->status
            );
        }

        if ($request->filled('date_from')) {
            $query->whereDate(
                'executed_at',
                '>=',
                $request->date_from
            );
        }

        if ($request->filled('date_to')) {
            $query->whereDate(
                'executed_at',
                '<=',
                $request->date_to
            );
        }

        $testRuns = $query
            ->latest('executed_at')
            ->get();

        $csv = '';

        $csv .= implode(',', [
            'ID',
            'Spec Name',
            'Test Name',
            'Status',
            'Browser',
            'Duration',
            'Error Message',
            'Executed At',
        ]) . "\n";

        foreach ($testRuns as $run) {
            $csv .= implode(',', [
                $run->id,
                $this->csvValue($run->spec_name),
                $this->csvValue($run->test_name),
                $run->status,
                $this->csvValue($run->browser),
                $run->duration ?? 0,
                $this->csvValue($run->error_message),
                $run->executed_at?->format(
                    'Y-m-d H:i:s'
                ),
            ]) . "\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header(
                'Content-Disposition',
                'attachment; filename="test-runs.csv"'
            );
    }

    /**
     * CSV escaping.
     */
    private function csvValue($value): string
    {
        $value = (string) ($value ?? '');

        return '"' .
            str_replace('"', '""', $value) .
            '"';
    }

    /**
     * Test Suite Analytics & Flaky Test Tracker.
     */
    public function analytics(Request $request): View
    {
        $totalRuns = TestRun::count();
        $passedRuns = TestRun::where('status', 'passed')->count();
        $failedRuns = TestRun::where('status', 'failed')->count();
        $skippedRuns = TestRun::where('status', 'skipped')->count();

        $passRate = $totalRuns > 0 ? round(($passedRuns / $totalRuns) * 100, 1) : 0;
        $failRate = $totalRuns > 0 ? round(($failedRuns / $totalRuns) * 100, 1) : 0;
        $skipRate = $totalRuns > 0 ? round(($skippedRuns / $totalRuns) * 100, 1) : 0;

        $avgDuration = TestRun::whereNotNull('duration')->avg('duration') ?? 0;
        $maxDuration = TestRun::whereNotNull('duration')->max('duration') ?? 0;
        $minDuration = TestRun::whereNotNull('duration')->min('duration') ?? 0;

        // Flaky Test Detection: Specs with both passed and failed records
        $flakySpecs = TestRun::selectRaw('spec_name, 
                SUM(CASE WHEN status = "passed" THEN 1 ELSE 0 END) as pass_count,
                SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as fail_count,
                COUNT(*) as total_count,
                AVG(duration) as avg_duration')
            ->groupBy('spec_name')
            ->havingRaw('SUM(CASE WHEN status = "passed" THEN 1 ELSE 0 END) > 0 AND SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) > 0')
            ->orderByRaw('(SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) / COUNT(*)) DESC')
            ->get();

        // Duration breakdown per spec
        $specDurations = TestRun::selectRaw('spec_name, AVG(duration) as avg_dur, MAX(duration) as max_dur, COUNT(*) as run_count')
            ->whereNotNull('duration')
            ->groupBy('spec_name')
            ->orderByDesc('avg_dur')
            ->limit(10)
            ->get();

        // Browser distribution
        $browserStats = TestRun::selectRaw('browser, COUNT(*) as count')
            ->groupBy('browser')
            ->orderByDesc('count')
            ->get();

        return view('test-runs.analytics', compact(
            'totalRuns',
            'passedRuns',
            'failedRuns',
            'skippedRuns',
            'passRate',
            'failRate',
            'skipRate',
            'avgDuration',
            'maxDuration',
            'minDuration',
            'flakySpecs',
            'specDurations',
            'browserStats'
        ));
    }
}