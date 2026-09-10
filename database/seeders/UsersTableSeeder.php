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


        // 3. Seed only the root Admin account
        $adminUser = User::updateOrCreate(
            ['email' => 'harutizwik@staff.msu.ac.zw'],
            [
                'name' => 'Admin User',
                'staff_id' => 'MSU001',
                'department' => 'IT',
                'position' => 'System Administrator',
                'approval_level' => 5,
                'password' => Hash::make('password123'),
            ]
        );
        $adminUser->syncRoles(['System Admin']);
    }
}
