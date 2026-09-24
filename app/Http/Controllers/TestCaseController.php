<?php

namespace App\Http\Controllers;

use App\Models\TestCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
            ->latest()
            ->paginate(10)
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
    public function update(Request $request, TestCase $testCase): RedirectResponse
    {
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
}