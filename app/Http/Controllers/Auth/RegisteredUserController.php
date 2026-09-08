<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        return Inertia::render('auth/register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class, 'ends_with:@staff.msu.ac.zw'],
            'staff_id' => 'required|string|max:50|unique:'.User::class,
            'department' => 'required|string|max:100',
            'position' => 'required|string|max:100',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'email.ends_with' => 'The email address must be an official staff email ending with @staff.msu.ac.zw.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'staff_id' => $request->staff_id ?? 'MSU' . random_int(1000, 9999),
            'department' => $request->department ?? 'General',
            'position' => $request->position ?? 'Staff',
            'approval_level' => 1,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return to_route('dashboard');
    }
}
