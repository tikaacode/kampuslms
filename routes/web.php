<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {

    Route::get('/profile', function () {
        return view('profile');
    })->name('profile');

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('courses', CourseController::class);

        Route::delete('/submissions/destroy-all', function () {
            \App\Models\Submission::truncate();
            return redirect()->back()->with('success', 'Semua submission dihapus.');
        })->name('submissions.destroy-all');
    });

    Route::middleware('role:dosen')->prefix('dosen')->name('dosen.')->scopeBindings()->group(function () {
        Route::resource('courses', CourseController::class);
        Route::resource('courses.materials', MaterialController::class);
    });

    Route::middleware('role:mahasiswa')->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        Route::resource('courses', CourseController::class)->only(['index', 'show']);
        Route::resource('submissions', SubmissionController::class)->only(['index', 'show', 'create', 'store']);
    });

});
