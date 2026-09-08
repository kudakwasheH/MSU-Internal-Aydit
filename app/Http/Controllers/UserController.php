<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rules;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('roles')->latest()->paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'staff_id' => ['required', 'string', 'max:20', 'unique:'.User::class],
            'department' => ['required', 'string', 'max:100'],
            'position' => ['required', 'string', 'max:100'],
            'approval_level' => ['required', 'integer', 'min:1', 'max:5'],
            'role' => ['required', 'exists:roles,name'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'staff_id' => $request->staff_id,
            'department' => $request->department,
            'position' => $request->position,
            'approval_level' => $request->approval_level,
        ]);

        $user->assignRole($request->role);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $userRole = $user->roles->first()?->name;
        return view('users.edit', compact('user', 'roles', 'userRole'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'staff_id' => ['required', 'string', 'max:20', 'unique:users,staff_id,'.$user->id],
            'department' => ['required', 'string', 'max:100'],
            'position' => ['required', 'string', 'max:100'],
            'approval_level' => ['required', 'integer', 'min:1', 'max:5'],
            'role' => ['required', 'exists:roles,name'],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'staff_id' => $request->staff_id,
            'department' => $request->department,
            'position' => $request->position,
            'approval_level' => $request->approval_level,
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Rules\Password::defaults()],
            ]);
            $user->update(['password' => Hash::make($request->password)]);
        }

        $user->syncRoles([$request->role]);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'You cannot delete your own active administrator account.');
        }

        try {
            DB::transaction(function () use ($user) {
                $adminId = auth()->id();
                $userId = $user->id;

                // 1. Detach pivot and permissions
                DB::table('audit_user')->where('user_id', $userId)->delete();
                $user->syncRoles([]);
                $user->syncPermissions([]);

                // 2. Clear nullable assignments
                DB::table('findings')->where('assigned_to', $userId)->update(['assigned_to' => null]);
                DB::table('action_items')->where('assigned_to', $userId)->update(['assigned_to' => null]);
                DB::table('risk_registers')->where('owner_id', $userId)->update(['owner_id' => null]);
                DB::table('audits')->where('approved_by', $userId)->update(['approved_by' => null]);
                DB::table('audit_logs')->where('user_id', $userId)->update(['user_id' => null]);

                if (Schema::hasTable('reports')) {
                    DB::table('reports')->where('reviewed_by', $userId)->update(['reviewed_by' => null]);
                    DB::table('reports')->where('approved_by', $userId)->update(['approved_by' => null]);
                    DB::table('reports')->where('prepared_by', $userId)->update(['prepared_by' => $adminId]);
                }

                // 3. Reassign non-nullable creator / reviewer / owner foreign keys to current admin
                DB::table('findings')->where('created_by', $userId)->update(['created_by' => $adminId]);
                DB::table('action_items')->where('created_by', $userId)->update(['created_by' => $adminId]);
                DB::table('working_papers')->where('created_by', $userId)->update(['created_by' => $adminId]);
                DB::table('quality_assessments')->where('reviewer_id', $userId)->update(['reviewer_id' => $adminId]);
                DB::table('escalations')->where('escalated_to', $userId)->update(['escalated_to' => $adminId]);
                DB::table('escalations')->where('escalated_by', $userId)->update(['escalated_by' => $adminId]);
                DB::table('risk_treatments')->where('owner_id', $userId)->update(['owner_id' => $adminId]);
                DB::table('audits')->where('created_by', $userId)->update(['created_by' => $adminId]);

                // 4. Delete the user
                $user->delete();
            });

            return redirect()->route('users.index')->with('success', 'User ' . $user->name . ' was successfully deleted.');
        } catch (\Throwable $e) {
            Log::error('User deletion error: ' . $e->getMessage());
            return redirect()->route('users.index')->with('error', 'Unable to delete user: ' . $e->getMessage());
        }
    }
}
