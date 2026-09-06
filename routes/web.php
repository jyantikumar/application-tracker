<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobApplicationController;

Route::get('/', function () {
    return redirect()->route('job-applications.index');
});

// Dedicated Applications List Route
Route::get('job-applications/list', [JobApplicationController::class, 'applications'])->name('job-applications.applications');

// Resource Routes for CRUD
Route::resource('job-applications', JobApplicationController::class);