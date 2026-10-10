<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'dosen', 'lecturer']);
    }

    public function update(User $user, Course $course): bool
    {
        return $user->role === 'admin' || $course->lecturer_id === $user->id;
    }

    public function delete(User $user, Course $course): bool
    {
        return $user->role === 'admin';
    }
}
