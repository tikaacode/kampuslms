<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    private function authorizeLecturer(Course $course): void
    {
        if ($course->lecturer_id !== auth()->id() && auth()->user()?->role !== 'admin') {
            abort(403, 'Anda bukan pengampu mata kuliah ini.');
        }
    }

    public function index(Course $course)
    {
        $this->authorizeLecturer($course);
        $materials = $course->materials()->orderBy('order')->get();
        return view('materials.index', compact('course', 'materials'));
    }

    public function show(Course $course, Material $material)
    {
        abort_if($material->course_id !== $course->id, 404);
        $this->authorizeLecturer($course);

        return view('materials.show', compact('course', 'material'));
    }

    public function create(Course $course)
    {
        $this->authorizeLecturer($course);
        return view('materials.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        $this->authorizeLecturer($course);

        $request->validate([
            'title'   => ['required', 'string', 'max:150'],
            'content' => ['nullable', 'string'],
            'order'   => ['nullable', 'integer'],
        ]);

        $material = $course->materials()->create($request->only(['title', 'content', 'order']));

        return redirect()->route('dosen.courses.materials.show', [$course, $material])
                         ->with('success', 'Materi berhasil ditambahkan.');
    }

    public function edit(Course $course, Material $material)
    {
        abort_if($material->course_id !== $course->id, 404);
        $this->authorizeLecturer($course);

        return view('materials.edit', compact('course', 'material'));
    }

    public function update(Request $request, Course $course, Material $material)
    {
        abort_if($material->course_id !== $course->id, 404);
        $this->authorizeLecturer($course);

        $request->validate([
            'title'   => ['required', 'string', 'max:150'],
            'content' => ['nullable', 'string'],
            'order'   => ['nullable', 'integer'],
        ]);

        $material->update($request->only(['title', 'content', 'order']));

        return redirect()->route('dosen.courses.materials.show', [$course, $material])
                         ->with('success', 'Materi berhasil diperbarui.');
    }

    public function destroy(Course $course, Material $material)
    {
        abort_if($material->course_id !== $course->id, 404);
        $this->authorizeLecturer($course);

        $material->delete();
        return redirect()->route('dosen.courses.materials.index', $course)
                         ->with('success', 'Materi berhasil dihapus.');
    }
}
