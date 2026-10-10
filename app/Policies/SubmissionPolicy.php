<?php

namespace App\Policies;

use App\Models\Submission;
use App\Models\User;

class SubmissionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Submission $submission): bool
    {
        if ($user->id === $submission->user_id || $user->role === 'admin') {
            return true;
        }

        if (in_array($user->role, ['dosen', 'lecturer'])) {
            $submission->loadMissing('assignment.course');
            return $submission->assignment?->course?->lecturer_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['mahasiswa', 'student']);
    }

    public function update(User $user, Submission $submission): bool
    {
        return $user->id === $submission->user_id;
    }

    public function delete(User $user, Submission $submission): bool
    {
        return $user->role === 'admin';
    }
}
