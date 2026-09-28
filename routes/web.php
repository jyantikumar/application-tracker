<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\InterviewController;
use App\Http\Controllers\OptionManagementController;
use App\Http\Controllers\CurrencyController;
use Illuminate\Support\Facades\Route;

// Redirect root directly to job applications (will trigger login redirect if unauthenticated)
Route::get('/', function () {
    return redirect()->route('job-applications.index');
});

// Authenticated & Verified Routes
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard route pointing to your main index page
    Route::get('/dashboard', function () {
        return redirect()->route('job-applications.index');
    })->name('dashboard');

    // Job Application Routes
    Route::get('job-applications/list', [JobApplicationController::class, 'applications'])->name('job-applications.applications');
    Route::resource('job-applications', JobApplicationController::class);

    // Currency list + PHP conversion
    Route::get('/currencies', [CurrencyController::class, 'Index'])->name('currencies.index');
    Route::get('/currencies/convert', [CurrencyController::class, 'Convert'])->name('currencies.convert');

    // Options — Sources
Route::get('/application-sources',              [OptionManagementController::class, 'indexSources'])->name('application-sources.index');
Route::post('/application-sources',             [OptionManagementController::class, 'storeSource'])->name('application-sources.store');
Route::put('/application-sources/{Source}',     [OptionManagementController::class, 'updateSource'])->name('application-sources.update');
Route::delete('/application-sources/{Source}',  [OptionManagementController::class, 'destroySource'])->name('application-sources.destroy');

// Options — Statuses
Route::get('/application-statuses',             [OptionManagementController::class, 'indexStatuses'])->name('application-statuses.index');
Route::post('/application-statuses',            [OptionManagementController::class, 'storeStatus'])->name('application-statuses.store');
Route::put('/application-statuses/{Status}',    [OptionManagementController::class, 'updateStatus'])->name('application-statuses.update');
Route::delete('/application-statuses/{Status}', [OptionManagementController::class, 'destroyStatus'])->name('application-statuses.destroy');
    // Interview Logging & Timeline Routes
    Route::post('job-applications/{JobApplication}/interviews', [InterviewController::class, 'Store'])->name('interviews.store');
    Route::put('interviews/{Interview}', [InterviewController::class, 'Update'])->name('interviews.update');
    Route::delete('interviews/{Interview}', [InterviewController::class, 'Destroy'])->name('interviews.destroy');

    Route::post('job-applications/import', [JobApplicationController::class, 'Import'])->name('job-applications.import');

    Route::get('job-applications/{JobApplication}/edit', [JobApplicationController::class, 'Edit'])->name('job-applications.edit');
    Route::put('job-applications/{JobApplication}', [JobApplicationController::class, 'Update'])->name('job-applications.update');

    // Breeze Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';