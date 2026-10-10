<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourseController;

Route::get('/', function () {
    return view('welcome');
});

// Route mata kuliah (sudah diperbaiki: GET bukan POST, dan /create ditaruh sebelum /{course})
Route::get('/courses',           [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/create',    [CourseController::class, 'create'])->name('courses.create');
Route::get('/courses/{course}',  [CourseController::class, 'show'])->name('courses.show');

