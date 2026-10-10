<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = match ($user->role) {
            'admin'                => Course::query(),
            'dosen', 'lecturer'    => $user->taughtCourses(),
            'mahasiswa', 'student' => $user->courses(),
            default                => Course::whereRaw('1 = 0'),
        };

        $courses = $query->with('lecturer')
            ->when($request->filled('q'), fn ($q) =>
                $q->where(fn ($sub) =>
                    $sub->where('name', 'like', "%{$request->q}%")
                        ->orWhere('code', 'like', "%{$request->q}%")
                )
            )
            ->paginate(15)
            ->withQueryString();

        return view('courses.index', compact('courses'));
    }

    public function create()
    {
        Gate::authorize('create', Course::class);

        $lecturers = User::whereIn('role', ['dosen', 'lecturer'])->get();
        return view('courses.create', compact('lecturers'));
    }

    public function store(StoreCourseRequest $request)
    {
        Gate::authorize('create', Course::class);

        $course = Course::create($request->validated());
        return redirect()->route('courses.show', $course)
                         ->with('success', 'Mata kuliah berhasil ditambahkan.');
    }

    public function show(Course $course)
    {
        $course->load('lecturer', 'materials', 'assignments');
        return view('courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        Gate::authorize('update', $course);

        $lecturers = User::whereIn('role', ['dosen', 'lecturer'])->get();
        return view('courses.edit', compact('course', 'lecturers'));
    }

    public function update(UpdateCourseRequest $request, Course $course)
    {
        Gate::authorize('update', $course);

        $course->update($request->validated());
        return redirect()->route('courses.show', $course)
                         ->with('success', 'Mata kuliah berhasil diperbarui.');
    }

    public function destroy(Course $course)
    {
        Gate::authorize('delete', $course);

        $course->delete();
        return redirect()->route('courses.index')
                         ->with('success', 'Mata kuliah berhasil dihapus.');
    }
}
