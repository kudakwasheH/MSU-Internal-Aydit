<?php

namespace Database\Seeders;

use App\Models\Audit;
use App\Models\RiskRegister;
use App\Models\User;
use Illuminate\Database\Seeder;

class AuditsTableSeeder extends Seeder
{
    public function run(): void
    {
        $auditor = User::where('email', 'auditor1@msu.ac.zw')->first();
        $manager = User::where('email', 'auditmanager@msu.ac.zw')->first();
        $risks = RiskRegister::all();

        $audits = [
            ['audit_code' => 'AUD-2024-0001', 'title' => 'Financial Statements Audit Q1', 'description' => 'Comprehensive audit of financial management processes and controls for Q1 2024', 'audit_type' => 'internal', 'priority' => 'high', 'status' => 'completed', 'planned_start_date' => '2024-01-15', 'planned_end_date' => '2024-02-15', 'actual_start_date' => '2024-01-15', 'actual_end_date' => '2024-02-20'],
            ['audit_code' => 'AUD-2024-0002', 'title' => 'IT Security Assessment', 'description' => 'Assessment of IT security controls and data protection measures across campus', 'audit_type' => 'it', 'priority' => 'high', 'status' => 'in_progress', 'planned_start_date' => '2024-02-01', 'planned_end_date' => '2024-03-01', 'actual_start_date' => '2024-02-05'],
            ['audit_code' => 'AUD-2024-0003', 'title' => 'ZIMCHE Compliance Review', 'description' => 'Review of compliance with ZIMCHE accreditation requirements', 'audit_type' => 'compliance', 'priority' => 'high', 'status' => 'completed', 'planned_start_date' => '2023-11-01', 'planned_end_date' => '2023-12-15', 'actual_start_date' => '2023-11-01', 'actual_end_date' => '2023-12-10'],
            ['audit_code' => 'AUD-2024-0004', 'title' => 'Procurement Process Audit', 'description' => 'Audit of procurement processes and vendor management procedures', 'audit_type' => 'performance', 'priority' => 'medium', 'status' => 'in_progress', 'planned_start_date' => '2024-03-01', 'planned_end_date' => '2024-04-15', 'actual_start_date' => '2024-03-05'],
            ['audit_code' => 'AUD-2024-0005', 'title' => 'HR Operations Review', 'description' => 'Review of HR operations including recruitment, payroll and leave management', 'audit_type' => 'internal', 'priority' => 'medium', 'status' => 'planned', 'planned_start_date' => '2024-04-01', 'planned_end_date' => '2024-05-01'],
            ['audit_code' => 'AUD-2024-0006', 'title' => 'Student Financial Aid Audit', 'description' => 'Audit of student bursary and financial aid disbursement processes', 'audit_type' => 'internal', 'priority' => 'medium', 'status' => 'planned', 'planned_start_date' => '2024-05-01', 'planned_end_date' => '2024-06-15'],
            ['audit_code' => 'AUD-2024-0007', 'title' => 'Network Infrastructure Audit', 'description' => 'Technical audit of campus network infrastructure and security', 'audit_type' => 'it', 'priority' => 'high', 'status' => 'draft', 'planned_start_date' => '2024-06-01', 'planned_end_date' => '2024-07-15'],
            ['audit_code' => 'AUD-2024-0008', 'title' => 'Research Grants Compliance', 'description' => 'Compliance audit of research grant utilization and reporting', 'audit_type' => 'compliance', 'priority' => 'medium', 'status' => 'completed', 'planned_start_date' => '2023-09-01', 'planned_end_date' => '2023-10-15', 'actual_start_date' => '2023-09-05', 'actual_end_date' => '2023-10-10'],
            ['audit_code' => 'AUD-2024-0009', 'title' => 'Asset Verification Exercise', 'description' => 'Physical verification and reconciliation of university fixed assets', 'audit_type' => 'internal', 'priority' => 'medium', 'status' => 'in_progress', 'planned_start_date' => '2024-03-15', 'planned_end_date' => '2024-04-30', 'actual_start_date' => '2024-03-15'],
            ['audit_code' => 'AUD-2024-0010', 'title' => 'Revenue Collection Audit', 'description' => 'Audit of tuition fee collection and reconciliation processes', 'audit_type' => 'internal', 'priority' => 'high', 'status' => 'planned', 'planned_start_date' => '2024-05-15', 'planned_end_date' => '2024-06-30'],
            ['audit_code' => 'AUD-2024-0011', 'title' => 'External Quality Assessment', 'description' => 'Five-year external quality assessment review engagement', 'audit_type' => 'external', 'priority' => 'high', 'status' => 'planned', 'planned_start_date' => '2024-07-01', 'planned_end_date' => '2024-08-31'],
            ['audit_code' => 'AUD-2024-0012', 'title' => 'Payroll Processing Audit', 'description' => 'Audit of salary computation, deductions, and payment processes', 'audit_type' => 'internal', 'priority' => 'high', 'status' => 'draft', 'planned_start_date' => '2024-08-01', 'planned_end_date' => '2024-09-15'],
            ['audit_code' => 'AUD-2024-0013', 'title' => 'Library Services Performance', 'description' => 'Performance audit of library services and digital resources', 'audit_type' => 'performance', 'priority' => 'low', 'status' => 'draft', 'planned_start_date' => '2024-09-01', 'planned_end_date' => '2024-10-15'],
            ['audit_code' => 'AUD-2024-0014', 'title' => 'Vehicle Fleet Audit', 'description' => 'Audit of vehicle fleet usage, maintenance and fuel management', 'audit_type' => 'internal', 'priority' => 'low', 'status' => 'cancelled', 'planned_start_date' => '2024-02-01', 'planned_end_date' => '2024-03-01'],
            ['audit_code' => 'AUD-2024-0015', 'title' => 'Data Protection Compliance', 'description' => 'Compliance audit against Zimbabwe Data Protection Act requirements', 'audit_type' => 'compliance', 'priority' => 'high', 'status' => 'planned', 'planned_start_date' => '2024-10-01', 'planned_end_date' => '2024-11-15'],
        ];

        foreach ($audits as $a) {
            $a['created_by'] = $auditor->id;
            if ($a['status'] !== 'draft') {
                $a['approved_by'] = $manager->id;
            }
            $audit = Audit::create($a);
            $audit->risks()->attach($risks->random(rand(1, 3))->pluck('id')->toArray());
        }
    }
}
