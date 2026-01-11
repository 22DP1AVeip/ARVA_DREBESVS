<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show(Request $request, ?string $section = 'settings')
    {
        $section = $section ?: 'settings';


        return Inertia::render('Profile/Index', [
            'user' => $request->user(),
            'section' => $section,
            'favorites' => [],
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        $request->user()->update($request->only('name', 'email'));

        return back()->with('success', 'Profila iestatījumi saglabāti.');
    }

    public function password(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Nepareiza parole.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Parole nomainīta.');
    }
}