<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeNewUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class, 'ends_with:@staff.msu.ac.zw'],
            'staff_id' => ['required', 'string', 'max:20', 'unique:'.User::class],
            'department' => ['required', 'string', 'max:100'],
            'position' => ['required', 'string', 'max:100'],
            'approval_level' => ['required', 'integer', 'min:1', 'max:5'],
            'role' => ['required', 'exists:roles,name'],
        ], [
            'email.ends_with' => 'The email address must be an official staff email ending with @staff.msu.ac.zw.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make(\Illuminate\Support\Str::random(32)),
            'staff_id' => $request->staff_id,
            'department' => $request->department,
            'position' => $request->position,
            'approval_level' => $request->approval_level,
        ]);

        $user->assignRole($request->role);

        // Send welcome email notification
        try {
            Mail::to($user->email)->send(new WelcomeNewUser($user, $request->role));
        } catch (\Throwable $e) {
            Log::warning('Welcome email failed for ' . $user->email . ': ' . $e->getMessage());
            return redirect()->route('users.index')->with('success', 'User created successfully, but the welcome email could not be sent.');
        }

        return redirect()->route('users.index')->with('success', 'User created successfully. A welcome email has been sent to ' . $user->email . '.');
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
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$user->id, 'ends_with:@staff.msu.ac.zw'],
            'staff_id' => ['required', 'string', 'max:20', 'unique:users,staff_id,'.$user->id],
            'department' => ['required', 'string', 'max:100'],
            'position' => ['required', 'string', 'max:100'],
            'approval_level' => ['required', 'integer', 'min:1', 'max:5'],
            'role' => ['required', 'exists:roles,name'],
        ], [
            'email.ends_with' => 'The email address must be an official staff email ending with @staff.msu.ac.zw.',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'staff_id' => $request->staff_id,
            'department' => $request->department,
            'position' => $request->position,
            'approval_level' => $request->approval_level,
        ]);

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
                if (Schema::hasTable('audit_user')) {
                    DB::table('audit_user')->where('user_id', $userId)->delete();
                }
                $user->syncRoles([]);
                $user->syncPermissions([]);

                // 2. Clear nullable assignments
                if (Schema::hasTable('findings') && Schema::hasColumn('findings', 'assigned_to')) {
                    DB::table('findings')->where('assigned_to', $userId)->update(['assigned_to' => null]);
                }
                if (Schema::hasTable('audits') && Schema::hasColumn('audits', 'approved_by')) {
                    DB::table('audits')->where('approved_by', $userId)->update(['approved_by' => null]);
                }
                if (Schema::hasTable('audit_logs') && Schema::hasColumn('audit_logs', 'user_id')) {
                    DB::table('audit_logs')->where('user_id', $userId)->update(['user_id' => null]);
                }

                if (Schema::hasTable('reports')) {
                    if (Schema::hasColumn('reports', 'senior_reviewer_id')) {
                        DB::table('reports')->where('senior_reviewer_id', $userId)->update(['senior_reviewer_id' => null]);
                    }
                    if (Schema::hasColumn('reports', 'chief_approver_id')) {
                        DB::table('reports')->where('chief_approver_id', $userId)->update(['chief_approver_id' => null]);
                    }
                    if (Schema::hasColumn('reports', 'prepared_by')) {
                        DB::table('reports')->where('prepared_by', $userId)->update(['prepared_by' => $adminId]);
                    }
                }

                // 3. Reassign non-nullable creator / reviewer / owner foreign keys to current admin
                if (Schema::hasTable('action_items') && Schema::hasColumn('action_items', 'assigned_to')) {
                    DB::table('action_items')->where('assigned_to', $userId)->update(['assigned_to' => $adminId]);
                }
                if (Schema::hasTable('risk_registers') && Schema::hasColumn('risk_registers', 'owner_id')) {
                    DB::table('risk_registers')->where('owner_id', $userId)->update(['owner_id' => $adminId]);
                }

                // 3. Reassign non-nullable creator / reviewer / owner foreign keys to current admin
                if (Schema::hasTable('findings') && Schema::hasColumn('findings', 'created_by')) {
                    DB::table('findings')->where('created_by', $userId)->update(['created_by' => $adminId]);
                }
                if (Schema::hasTable('action_items') && Schema::hasColumn('action_items', 'created_by')) {
                    DB::table('action_items')->where('created_by', $userId)->update(['created_by' => $adminId]);
                }
                if (Schema::hasTable('working_papers') && Schema::hasColumn('working_papers', 'created_by')) {
                    DB::table('working_papers')->where('created_by', $userId)->update(['created_by' => $adminId]);
                }
                if (Schema::hasTable('quality_assessments') && Schema::hasColumn('quality_assessments', 'reviewer_id')) {
                    DB::table('quality_assessments')->where('reviewer_id', $userId)->update(['reviewer_id' => $adminId]);
                }
                if (Schema::hasTable('escalations')) {
                    if (Schema::hasColumn('escalations', 'escalated_to')) {
                        DB::table('escalations')->where('escalated_to', $userId)->update(['escalated_to' => $adminId]);
                    }
                    if (Schema::hasColumn('escalations', 'escalated_by')) {
                        DB::table('escalations')->where('escalated_by', $userId)->update(['escalated_by' => $adminId]);
                    }
                }
                if (Schema::hasTable('risk_treatments') && Schema::hasColumn('risk_treatments', 'owner_id')) {
                    DB::table('risk_treatments')->where('owner_id', $userId)->update(['owner_id' => $adminId]);
                }
                if (Schema::hasTable('audits') && Schema::hasColumn('audits', 'created_by')) {
                    DB::table('audits')->where('created_by', $userId)->update(['created_by' => $adminId]);
                }

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
