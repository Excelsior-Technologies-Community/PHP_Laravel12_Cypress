<?php

namespace App\Http\Controllers;

use App\Models\TestRun;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

        $recentRuns = TestRun::latest('executed_at')
            ->latest()
            ->limit(10)
            ->get();

        return view('test-runs.dashboard', compact(
            'total',
            'passed',
            'failed',
            'skipped',
            'passRate',
            'averageDuration',
            'recentRuns'
        ));
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

        $testRuns = $query
            ->latest('executed_at')
            ->paginate(15)
            ->withQueryString();

        return view('test-runs.index', compact('testRuns'));
    }

    /**
     * Store a Cypress test result.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'spec_name' => ['required', 'string', 'max:255'],
            'test_name' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:passed,failed,skipped'],
            'browser' => ['nullable', 'string', 'max:100'],
            'duration' => ['nullable', 'integer', 'min:0'],
            'error_message' => ['nullable', 'string'],
            'executed_at' => ['nullable', 'date'],
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
}