<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit', ['user' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        auth()->user()->update($request->only(['name', 'email']));

        return redirect()->route('profile.edit')
                         ->with('success', 'Profil berhasil diperbarui.');
    }
}
