<?php

namespace App\Http\Controllers;

use App\Models\TestCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class TestCaseController extends Controller
{
    /**
     * Display test cases.
     */
    public function index(Request $request): View
    {
        $query = TestCase::query();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('module', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $testCases = $query
            ->oldest()
            ->paginate(5)
            ->withQueryString();

        return view('test-cases.index', compact('testCases'));
    }

    /**
     * Show create form.
     */
    public function create(): View
    {
        return view('test-cases.create');
    }

    /**
     * Store a test case.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'module' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', 'in:low,medium,high'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        TestCase::create($validated);

        return redirect()
            ->route('test-cases.index')
            ->with('success', 'Test case created successfully.');
    }

    /**
     * Show test case.
     */
    public function show(TestCase $testCase): View
    {
        return view('test-cases.show', compact('testCase'));
    }

    /**
     * Show edit form.
     */
    public function edit(TestCase $testCase): View
    {
        return view('test-cases.edit', compact('testCase'));
    }

    /**
     * Update test case.
     */
    public function update(
        Request $request,
        TestCase $testCase
    ): RedirectResponse {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'module' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', 'in:low,medium,high'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $testCase->update($validated);

        return redirect()
            ->route('test-cases.index')
            ->with('success', 'Test case updated successfully.');
    }

    /**
     * Delete test case.
     */
    public function destroy(TestCase $testCase): RedirectResponse
    {
        $testCase->delete();

        return redirect()
            ->route('test-cases.index')
            ->with('success', 'Test case deleted successfully.');
    }

    /**
     * Bulk delete test cases.
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:test_cases,id'],
        ]);

        $count = TestCase::whereIn('id', $validated['ids'])->delete();

        return redirect()
            ->route('test-cases.index')
            ->with(
                'success',
                "{$count} test case(s) deleted successfully."
            );
    }

    /**
     * Duplicate / clone a test case.
     */
    public function duplicate(TestCase $testCase): RedirectResponse
    {
        $copy = $testCase->replicate();

        $copy->title = $testCase->title . ' - Copy';

        $copy->save();

        return redirect()
            ->route('test-cases.index')
            ->with(
                'success',
                'Test case duplicated successfully.'
            );
    }

    /**
     * Export test cases to CSV.
     */
    public function export(Request $request): Response
    {
        $query = TestCase::query();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('module', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $testCases = $query->latest()->get();

        $csv = '';

        $csv .= implode(',', [
            'ID',
            'Title',
            'Module',
            'Description',
            'Priority',
            'Status',
            'Created At',
        ]) . "\n";

        foreach ($testCases as $testCase) {
            $csv .= implode(',', [
                $testCase->id,
                $this->csvValue($testCase->title),
                $this->csvValue($testCase->module),
                $this->csvValue($testCase->description),
                $testCase->priority,
                $testCase->status,
                $testCase->created_at?->format('Y-m-d H:i:s'),
            ]) . "\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header(
                'Content-Disposition',
                'attachment; filename="test-cases.csv"'
            );
    }

    /**
     * Test case statistics.
     */
    public function statistics(): View
    {
        $total = TestCase::count();

        $active = TestCase::where('status', 'active')->count();

        $inactive = TestCase::where('status', 'inactive')->count();

        $low = TestCase::where('priority', 'low')->count();

        $medium = TestCase::where('priority', 'medium')->count();

        $high = TestCase::where('priority', 'high')->count();

        $modules = TestCase::selectRaw(
            'module, COUNT(*) as total'
        )
            ->whereNotNull('module')
            ->groupBy('module')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $recentCases = TestCase::latest()
            ->limit(5)
            ->get();

        return view(
            'test-cases.statistics',
            compact(
                'total',
                'active',
                'inactive',
                'low',
                'medium',
                'high',
                'modules',
                'recentCases'
            )
        );
    }

    /**
     * Safely prepare CSV value.
     */
    private function csvValue($value): string
    {
        $value = (string) ($value ?? '');

        return '"' . str_replace('"', '""', $value) . '"';
    }
}