@extends('test-cases.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark">⚡ Cypress Spec Generator & Code Exporter</h2>
        <p class="text-muted mb-0">Generate ready-to-run Cypress E2E JavaScript spec files (.cy.js) automatically from test cases.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('test-cases.index') }}" class="btn btn-outline-secondary">
            ← Back to Test Cases
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Test Case Selection Panel -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-dark text-white fw-bold">
                1. Select Target Test Case
            </div>
            <div class="card-body">
                <form action="{{ route('cypress.spec-studio') }}" method="GET" class="mb-3">
                    <label for="test_case_id" class="form-label fw-bold">Select Active Test Case:</label>
                    <select name="test_case_id" id="test_case_id" class="form-select mb-3" onchange="this.form.submit()">
                        <option value="">-- Choose Test Case --</option>
                        @foreach($testCases as $tc)
                            <option value="{{ $tc->id }}" {{ optional($selectedCase)->id == $tc->id ? 'selected' : '' }}>
                                [{{ $tc->module ?: 'General' }}] {{ $tc->title }}
                            </option>
                        @endforeach
                    </select>
                    <noscript>
                        <button type="submit" class="btn btn-primary w-100">Load Test Case</button>
                    </noscript>
                </form>

                @if($selectedCase)
                    <div class="border rounded p-3 bg-light">
                        <h6 class="fw-bold text-primary mb-2">{{ $selectedCase->title }}</h6>
                        <p class="mb-1 text-muted small"><strong>Module:</strong> {{ $selectedCase->module ?: 'General' }}</p>
                        <p class="mb-1 text-muted small"><strong>Priority:</strong> <span class="badge bg-{{ $selectedCase->priority == 'high' ? 'danger' : ($selectedCase->priority == 'medium' ? 'warning' : 'success') }}">{{ ucfirst($selectedCase->priority) }}</span></p>
                        <hr class="my-2">
                        <p class="mb-1 text-muted small"><strong>Steps / Description:</strong></p>
                        <div class="small bg-white p-2 border rounded" style="max-height: 150px; overflow-y: auto;">
                            {!! nl2br(e($selectedCase->description ?: 'No detailed steps provided.')) !!}
                        </div>
                    </div>
                @else
                    <div class="alert alert-info mb-0">
                        Please select a test case from the dropdown to preview and export spec code.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Generated Code & Actions Panel -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <span>2. Cypress Spec JavaScript Code Output</span>
                @if($selectedCase)
                    <span class="badge bg-success font-monospace">{{ \Illuminate\Support\Str::slug($selectedCase->title) }}.cy.js</span>
                @endif
            </div>
            <div class="card-body">
                @if($selectedCase && $generatedCode)
                    <div class="d-flex gap-2 mb-3">
                        <button type="button" class="btn btn-success btn-sm d-flex align-items-center gap-1" onclick="copySpecCode()">
                            📋 Copy Code to Clipboard
                        </button>
                        <a href="{{ route('test-cases.download-spec', $selectedCase->id) }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                            💾 Download .cy.js File
                        </a>
                    </div>
                    <div id="copyAlert" class="alert alert-success py-1 px-3 mb-2 d-none small">
                        ✅ Spec code copied to clipboard successfully!
                    </div>

                    <textarea id="specCodeArea" class="form-control font-monospace bg-dark text-light p-3 border-0" rows="18" readonly style="font-family: Consolas, monospace; font-size: 13px; line-height: 1.5;">{{ $generatedCode }}</textarea>
                @else
                    <div class="text-center text-muted py-5">
                        <h4>No Test Case Selected</h4>
                        <p>Select a test case from the left panel to auto-generate Cypress E2E JavaScript code.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function copySpecCode() {
    const codeArea = document.getElementById('specCodeArea');
    if (!codeArea) return;
    navigator.clipboard.writeText(codeArea.value).then(() => {
        const alertBox = document.getElementById('copyAlert');
        alertBox.classList.remove('d-none');
        setTimeout(() => alertBox.classList.add('d-none'), 3000);
    }).catch(err => {
        alert('Failed to copy: ' + err);
    });
}
</script>
@endsection
