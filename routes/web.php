<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TestCaseController;
use App\Http\Controllers\TestRunController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware([
    'auth',
    'verified',
])->name('dashboard');

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [
        ProfileController::class,
        'edit',
    ])->name('profile.edit');

    Route::patch('/profile', [
        ProfileController::class,
        'update',
    ])->name('profile.update');

    Route::delete('/profile', [
        ProfileController::class,
        'destroy',
    ])->name('profile.destroy');


    /*
    |--------------------------------------------------------------------------
    | Test Cases
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'test-cases',
        TestCaseController::class
    );

    /*
    | Functionality 1
    | Bulk Delete
    */

    Route::delete(
        '/test-cases-bulk-delete',
        [
            TestCaseController::class,
            'bulkDestroy',
        ]
    )->name('test-cases.bulk-destroy');

    /*
    | Functionality 2
    | Duplicate
    */

    Route::post(
        '/test-cases/{testCase}/duplicate',
        [
            TestCaseController::class,
            'duplicate',
        ]
    )->name('test-cases.duplicate');

    /*
    | Functionality 3
    | CSV Export
    */

    Route::get(
        '/test-cases-export',
        [
            TestCaseController::class,
            'export',
        ]
    )->name('test-cases.export');

    /*
    | Functionality 4
    | Statistics
    */

    Route::get(
        '/test-cases-statistics',
        [
            TestCaseController::class,
            'statistics',
        ]
    )->name('test-cases.statistics');


    /*
    |--------------------------------------------------------------------------
    | Test Runs
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/test-runs/dashboard',
        [
            TestRunController::class,
            'dashboard',
        ]
    )->name('test-runs.dashboard');

    Route::get(
        '/test-runs',
        [
            TestRunController::class,
            'index',
        ]
    )->name('test-runs.index');

    Route::post(
        '/test-runs',
        [
            TestRunController::class,
            'store',
        ]
    )->name('test-runs.store');

    /*
    | Functionality 5
    | Bulk Delete
    */

    Route::delete(
        '/test-runs-bulk-delete',
        [
            TestRunController::class,
            'bulkDestroy',
        ]
    )->name('test-runs.bulk-destroy');

    /*
    | Functionality 6
    | CSV Export
    */

    Route::get(
        '/test-runs-export',
        [
            TestRunController::class,
            'export',
        ]
    )->name('test-runs.export');

    /*
    |--------------------------------------------------------------------------
    | NEW: Cypress Spec Generator & Exporter Studio
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/cypress-spec-generator',
        [
            TestCaseController::class,
            'specStudio',
        ]
    )->name('cypress.spec-studio');

    Route::get(
        '/test-cases/{testCase}/generate-spec',
        [
            TestCaseController::class,
            'generateSpec',
        ]
    )->name('test-cases.generate-spec');

    Route::get(
        '/test-cases/{testCase}/download-spec',
        [
            TestCaseController::class,
            'downloadSpec',
        ]
    )->name('test-cases.download-spec');

    /*
    |--------------------------------------------------------------------------
    | NEW: Test Suite Analytics & Flaky Test Tracker
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/test-analytics',
        [
            TestRunController::class,
            'analytics',
        ]
    )->name('test-runs.analytics');
});

require __DIR__ . '/auth.php';