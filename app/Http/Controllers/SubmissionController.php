<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SubmissionController extends Controller
{
    public function index()
    {
        $submissions = auth()->user()->submissions()->with('assignment.course')->latest()->get();
        return view('submissions.index', compact('submissions'));
    }

    public function show(Submission $submission)
    {
        Gate::authorize('view', $submission);

        return view('submissions.show', compact('submission'));
    }

    public function create()
    {
        return view('submissions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'assignment_id' => ['required', 'exists:assignments,id'],
            'note'          => ['nullable', 'string'],
        ]);

        $submission = Submission::create([
            'assignment_id' => $request->assignment_id,
            'user_id'       => auth()->id(),
            'note'          => $request->note,
            'submitted_at'  => now(),
        ]);

        return redirect()->route('submissions.show', $submission)
                         ->with('success', 'Tugas berhasil dikumpulkan.');
    }
}
