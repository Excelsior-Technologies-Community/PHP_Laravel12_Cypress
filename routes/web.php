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
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [
        ProfileController::class,
        'edit'
    ])->name('profile.edit');

    Route::patch('/profile', [
        ProfileController::class,
        'update'
    ])->name('profile.update');

    Route::delete('/profile', [
        ProfileController::class,
        'destroy'
    ])->name('profile.destroy');


    /*
    |--------------------------------------------------------------------------
    | Cypress Test Cases
    |--------------------------------------------------------------------------
    */

    Route::resource('test-cases', TestCaseController::class);


    /*
    |--------------------------------------------------------------------------
    | Cypress Test Runs
    |--------------------------------------------------------------------------
    */

    Route::get('/test-runs/dashboard', [
        TestRunController::class,
        'dashboard'
    ])->name('test-runs.dashboard');

    Route::get('/test-runs', [
        TestRunController::class,
        'index'
    ])->name('test-runs.index');

    Route::post('/test-runs', [
        TestRunController::class,
        'store'
    ])->name('test-runs.store');

});

require __DIR__.'/auth.php';