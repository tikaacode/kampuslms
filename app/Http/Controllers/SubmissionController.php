<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function index()
    {
        // Hanya tampilkan submission milik user yang login
        $submissions = auth()->user()->submissions()->with('assignment')->latest()->get();
        return view('submissions.index', compact('submissions'));
    }

    public function show(Submission $submission)
    {
        // Cegah IDOR: hanya pemilik submission yang boleh mengakses
        abort_if($submission->user_id !== auth()->id() && auth()->user()->role !== 'admin', 403, 'Anda tidak berhak melihat submission ini.');

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

        return redirect()->route('mahasiswa.submissions.show', $submission)
                         ->with('success', 'Tugas berhasil dikumpulkan.');
    }
}
