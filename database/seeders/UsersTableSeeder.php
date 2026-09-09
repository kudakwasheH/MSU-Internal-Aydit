<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Core Permissions Map
        $permissions = [
            'view audits', 'create audits', 'edit audits', 'delete audits', 'approve audits',
            'view risks', 'create risks', 'edit risks', 'delete risks', 'validate risks', 'integrate risk-register',
            'view findings', 'create findings', 'edit findings', 'delete findings', 'escalate findings', 'view recurring-findings',
            'view action-items', 'create action-items', 'edit action-items', 'delete action-items', 'complete action-items',
            'view working-papers', 'create working-papers', 'edit working-papers', 'approve working-papers',
            'upload evidence', 'manage audit-templates', 'manage change-control',
            'view quality-assessments', 'create quality-assessments',
            'generate reports', 'view reports', 'export reports', 'view cycle-time-analytics',
            'view dashboard', 'view system-performance',
            'view committee-dashboard', 'view executive-dashboard', 'view board-dashboard', 'view governance-dashboards',
            'view budget-integrations', 'view financial-controls', 'view budget-linkage',
            'manage users', 'manage roles', 'view audit-logs', 'manage integrations',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // 2. Map Permissions to Roles (Based on BRD)
        
        $admin = Role::firstOrCreate(['name' => 'System Admin']);
        $admin->givePermissionTo(Permission::all());

        // 1. Internal Auditor / Audit Team Member
        $auditor = Role::firstOrCreate(['name' => 'Auditor']);
        $auditor->givePermissionTo([
            'view audits', 'view working-papers', 'create working-papers', 'edit working-papers',
            'upload evidence', 'manage audit-templates', 'view findings', 'create findings', 'edit findings', 'view action-items', 'view dashboard',
            'view reports', 'generate reports', 'export reports',
        ]);

        // 2. Audit Management (Audit Manager / Head of Audit)
        $auditManager = Role::firstOrCreate(['name' => 'Audit Manager']);
        $auditManager->givePermissionTo([
            'view audits', 'create audits', 'edit audits', 'approve audits',
            'view findings', 'edit findings', 'delete findings', 'escalate findings', 'view recurring-findings',
            'approve working-papers', 'view quality-assessments', 'create quality-assessments',
            'generate reports', 'view reports', 'export reports', 'view cycle-time-analytics', 'manage change-control', 'view dashboard',
        ]);

        // 3. Risk Department
        $riskOfficer = Role::firstOrCreate(['name' => 'Risk Officer']);
        $riskOfficer->givePermissionTo([
            'view risks', 'create risks', 'edit risks', 'delete risks', 
            'integrate risk-register', 'validate risks', 'view audits', 'view dashboard'
        ]);

        // 4. Audit Committee / Governance Oversight
        $committee = Role::firstOrCreate(['name' => 'Audit Committee']);
        $committee->givePermissionTo([
            'view audits', 'view reports', 'view findings', 'view recurring-findings', 'view dashboard', 'view committee-dashboard',
        ]);

        // 5. Council / Board
        $board = Role::firstOrCreate(['name' => 'Council / Board']);
        $board->givePermissionTo([
            'view reports', 'view governance-dashboards', 'view board-dashboard', 'view cycle-time-analytics'
        ]);

        // 6. IT Department / System Administrator
        $it = Role::firstOrCreate(['name' => 'IT Department']);
        $it->givePermissionTo([
            'manage users', 'manage roles', 'view audit-logs', 'manage integrations', 'view system-performance', 'view dashboard'
        ]);

        // 7. Finance Department
        $finance = Role::firstOrCreate(['name' => 'Finance Department']);
        $finance->givePermissionTo([
            'view budget-integrations', 'view financial-controls', 'view budget-linkage', 'view dashboard'
        ]);

        // 8. Executive Management
        $executive = Role::firstOrCreate(['name' => 'Executive Management']);
        $executive->givePermissionTo([
            'view reports', 'view executive-dashboard', 'view cycle-time-analytics', 'view dashboard'
        ]);


        // 3. Seed Root Users for each
        $users = [
            ['name' => 'Admin User', 'email' => 'admin@staff.msu.ac.zw', 'staff_id' => 'MSU001', 'department' => 'IT', 'position' => 'System Administrator', 'approval_level' => 4, 'role' => 'System Admin'],
            ['name' => 'John Moyo', 'email' => 'auditmanager@staff.msu.ac.zw', 'staff_id' => 'MSU002', 'department' => 'Internal Audit', 'position' => 'Audit Manager', 'approval_level' => 3, 'role' => 'Audit Manager'],
            ['name' => 'Tendai Chipere', 'email' => 'auditor1@staff.msu.ac.zw', 'staff_id' => 'MSU003', 'department' => 'Internal Audit', 'position' => 'Senior Auditor', 'approval_level' => 2, 'role' => 'Auditor'],
            ['name' => 'Grace Ncube', 'email' => 'riskofficer@staff.msu.ac.zw', 'staff_id' => 'MSU004', 'department' => 'Risk Management', 'position' => 'Risk Officer', 'approval_level' => 2, 'role' => 'Risk Officer'],
            ['name' => 'Prof. Sibanda', 'email' => 'committee@staff.msu.ac.zw', 'staff_id' => 'MSU005', 'department' => 'Audit Committee', 'position' => 'Committee Chair', 'approval_level' => 4, 'role' => 'Audit Committee'],
            ['name' => 'Chairman Nkomo', 'email' => 'board@staff.msu.ac.zw', 'staff_id' => 'MSU006', 'department' => 'Council', 'position' => 'Board Chairman', 'approval_level' => 4, 'role' => 'Council / Board'],
            ['name' => 'IT Lead', 'email' => 'itadmin@staff.msu.ac.zw', 'staff_id' => 'MSU007', 'department' => 'IT Services', 'position' => 'Systems Admin', 'approval_level' => 4, 'role' => 'IT Department'],
            ['name' => 'Director Finance', 'email' => 'finance@staff.msu.ac.zw', 'staff_id' => 'MSU008', 'department' => 'Finance', 'position' => 'Finance Director', 'approval_level' => 4, 'role' => 'Finance Department'],
            ['name' => 'Dr. Chigwedere', 'email' => 'executive@staff.msu.ac.zw', 'staff_id' => 'MSU009', 'department' => 'Executive Office', 'position' => 'Vice Chancellor', 'approval_level' => 4, 'role' => 'Executive Management'],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);
            $userData['password'] = Hash::make('password123');
            
            // Allow rerunning seeder and updating existing user records seamlessly
            $user = User::updateOrCreate(['staff_id' => $userData['staff_id']], $userData);
            $user->syncRoles([$role]); // Ensure exact matching roles
        }
    }
}
