<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\InterviewController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root URL directly to the dashboard
Route::get('/', function () {
    return redirect()->route('job-applications.index');
});

// Dedicated Applications List View Route
Route::get('job-applications/list', [JobApplicationController::class, 'applications'])->name('job-applications.applications');

// Job Applications Resource Routes (Index, Create, Store, Show, Edit, Update, Destroy)
Route::resource('job-applications', JobApplicationController::class);

// Interview Logging & Timeline Routes
Route::post('job-applications/{JobApplication}/interviews', [InterviewController::class, 'Store'])->name('interviews.store');
Route::put('interviews/{Interview}', [InterviewController::class, 'Update'])->name('interviews.update');
Route::delete('interviews/{Interview}', [InterviewController::class, 'Destroy'])->name('interviews.destroy');

Route::post('job-applications/import', [JobApplicationController::class, 'Import'])->name('job-applications.import');

Route::get('job-applications/{JobApplication}/edit', [JobApplicationController::class, 'Edit'])->name('job-applications.edit');
Route::put('job-applications/{JobApplication}', [JobApplicationController::class, 'Update'])->name('job-applications.update');