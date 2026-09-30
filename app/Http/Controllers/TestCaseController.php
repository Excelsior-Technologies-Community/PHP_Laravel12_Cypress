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

    /**
     * Cypress Spec Studio view.
     */
    public function specStudio(Request $request): View
    {
        $testCases = TestCase::where('status', 'active')->orderBy('title')->get();
        $selectedCase = null;
        $generatedCode = '';

        if ($request->filled('test_case_id')) {
            $selectedCase = TestCase::find($request->test_case_id);
        } else {
            $selectedCase = $testCases->first();
        }

        if ($selectedCase) {
            $generatedCode = $this->buildCypressCode($selectedCase);
        }

        return view('test-cases.spec_studio', compact('testCases', 'selectedCase', 'generatedCode'));
    }

    /**
     * Generate spec code via AJAX/View.
     */
    public function generateSpec(TestCase $testCase): View
    {
        $testCases = TestCase::where('status', 'active')->orderBy('title')->get();
        $selectedCase = $testCase;
        $generatedCode = $this->buildCypressCode($testCase);

        return view('test-cases.spec_studio', compact('testCases', 'selectedCase', 'generatedCode'));
    }

    /**
     * Download .cy.js file.
     */
    public function downloadSpec(TestCase $testCase): Response
    {
        $code = $this->buildCypressCode($testCase);
        $filename = \Illuminate\Support\Str::slug($testCase->title) . '.cy.js';

        return response($code)
            ->header('Content-Type', 'application/javascript')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    /**
     * Build Cypress E2E JS Spec code for a test case.
     */
    public function buildCypressCode(TestCase $testCase): string
    {
        $module = $testCase->module ?: 'General';
        $descLines = array_filter(explode("\n", $testCase->description ?? ''));

        $spec = "/**\n";
        $spec .= " * Auto-Generated Cypress E2E Spec Code\n";
        $spec .= " * Test Case ID: {$testCase->id}\n";
        $spec .= " * Title: {$testCase->title}\n";
        $spec .= " * Module: {$module}\n";
        $spec .= " * Priority: {$testCase->priority}\n";
        $spec .= " */\n\n";

        $spec .= "describe('Module: " . addslashes($module) . " - " . addslashes($testCase->title) . "', () => {\n";
        $spec .= "    beforeEach(() => {\n";
        $spec .= "        // Reset viewport for standard desktop E2E run\n";
        $spec .= "        cy.viewport(1280, 720);\n";
        $spec .= "    });\n\n";

        $spec .= "    it('should successfully execute E2E steps for: " . addslashes($testCase->title) . "', () => {\n";
        $moduleSlug = strtolower(str_replace(' ', '-', $module));
        $spec .= "        // Step 1: Visit target module route\n";
        $spec .= "        cy.visit('/{$moduleSlug}');\n";
        $spec .= "        cy.url().should('include', '/{$moduleSlug}');\n\n";

        if (!empty($descLines)) {
            $spec .= "        // Executing custom test case steps:\n";
            foreach ($descLines as $idx => $line) {
                $line = trim($line);
                if (empty($line)) continue;
                $stepNum = $idx + 1;
                $spec .= "        // Step {$stepNum}: " . addslashes($line) . "\n";
                if (str_contains(strtolower($line), 'click') || str_contains(strtolower($line), 'button')) {
                    $spec .= "        cy.contains('" . addslashes($line) . "').click();\n";
                } elseif (str_contains(strtolower($line), 'input') || str_contains(strtolower($line), 'fill') || str_contains(strtolower($line), 'enter')) {
                    $spec .= "        cy.get('input[type=\"text\"]').first().type('" . addslashes($line) . "');\n";
                } else {
                    $spec .= "        cy.contains('" . addslashes($line) . "').should('exist');\n";
                }
            }
        } else {
            $spec .= "        // Default verification assertion\n";
            $spec .= "        cy.get('body').should('be.visible');\n";
            $spec .= "        cy.contains('" . addslashes($testCase->title) . "').should('exist');\n";
        }

        $spec .= "    });\n";
        $spec .= "});\n";

        return $spec;
    }
}