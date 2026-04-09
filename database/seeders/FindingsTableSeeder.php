<?php

namespace Database\Seeders;

use App\Models\Audit;
use App\Models\Finding;
use App\Models\User;
use Illuminate\Database\Seeder;

class FindingsTableSeeder extends Seeder
{
    public function run(): void
    {
        $auditor = User::where('email', 'auditor1@msu.ac.zw')->first();
        $audits = Audit::whereIn('status', ['in_progress', 'completed'])->get();

        $findings = [
            ['title' => 'Inadequate Segregation of Duties', 'description' => 'Same staff member handles authorization and recording of transactions in Finance department', 'root_cause' => 'Limited staff capacity and lack of awareness of control requirements', 'impact' => 'Increased risk of errors and potential fraud going undetected', 'recommendation' => 'Implement segregation of duties policy and cross-training program', 'severity' => 'high'],
            ['title' => 'Outdated IT Security Policies', 'description' => 'IT security policies have not been updated in over 3 years, leaving gaps in current threat coverage', 'root_cause' => 'Lack of regular policy review process and dedicated security personnel', 'impact' => 'University systems vulnerable to modern cyber threats and attacks', 'recommendation' => 'Establish annual IT security policy review cycle and hire dedicated security officer', 'severity' => 'critical'],
            ['title' => 'Incomplete Working Paper Documentation', 'description' => 'Working papers lack proper documentation of testing procedures and conclusions', 'root_cause' => 'Insufficient training on documentation standards and time pressure', 'impact' => 'Difficulty in audit trail verification and peer review process', 'recommendation' => 'Develop documentation templates and provide training on standards', 'severity' => 'medium'],
            ['title' => 'Delayed Implementation of Prior Recommendations', 'description' => '60% of previous audit recommendations not implemented within agreed timelines', 'root_cause' => 'No formal tracking mechanism and lack of accountability framework', 'impact' => 'Recurring issues persist leading to increased organizational risk', 'recommendation' => 'Implement automated action tracking system with escalation triggers', 'severity' => 'high'],
            ['title' => 'Weak Password Policies', 'description' => 'University systems allow weak passwords and lack multi-factor authentication', 'root_cause' => 'Legacy system constraints and user resistance to change', 'impact' => 'High risk of unauthorized access to sensitive academic and financial data', 'recommendation' => 'Implement MFA and enforce minimum password complexity standards', 'severity' => 'high'],
            ['title' => 'Procurement Approval Bypass', 'description' => 'Several purchases made without following the required three-quote procedure', 'root_cause' => 'Emergency procurement clause being overused without proper justification', 'impact' => 'Loss of value for money and potential for procurement fraud', 'recommendation' => 'Restrict emergency procurement authority and require post-facto review', 'severity' => 'critical'],
            ['title' => 'Incomplete Fixed Asset Register', 'description' => 'Physical asset verification revealed 15% of assets not recorded in register', 'root_cause' => 'Decentralized asset acquisition without central notification', 'impact' => 'Risk of asset misappropriation and inaccurate financial statements', 'recommendation' => 'Centralize asset recording process and conduct quarterly reconciliation', 'severity' => 'medium'],
            ['title' => 'Student Financial Aid Over-allocation', 'description' => 'Some students receiving bursaries exceeding their actual financial need', 'root_cause' => 'Outdated means-testing criteria and manual verification process', 'impact' => 'Inequitable distribution of scarce financial aid resources', 'recommendation' => 'Update means-testing criteria and implement automated verification', 'severity' => 'medium'],
            ['title' => 'No Disaster Recovery Testing', 'description' => 'Disaster recovery plan exists but has never been tested or validated', 'root_cause' => 'Budget constraints and fear of service disruption during testing', 'impact' => 'Unknown recovery capability in the event of actual disaster', 'recommendation' => 'Schedule annual DR testing during low-activity periods', 'severity' => 'high'],
            ['title' => 'Excess Leave Accumulation', 'description' => 'Multiple staff members have accumulated leave exceeding 90 days', 'root_cause' => 'No enforcement of leave utilization policy by department heads', 'impact' => 'Significant financial liability and staff burnout risk', 'recommendation' => 'Enforce mandatory leave utilization and cap maximum accumulation', 'severity' => 'low'],
        ];

        $i = 0;
        foreach ($audits as $audit) {
            foreach ($findings as $f) {
                if (rand(0, 2) > 0) { // ~66% chance
                    $f['audit_id'] = $audit->id;
                    $f['created_by'] = $auditor->id;
                    $f['assigned_to'] = $auditor->id;
                    $f['status'] = ['open', 'in_progress', 'resolved', 'closed'][array_rand(['open', 'in_progress', 'resolved', 'closed'])];
                    $f['escalation_level'] = ($f['severity'] === 'high' || $f['severity'] === 'critical') ? rand(1, 3) : 1;
                    Finding::create($f);
                    $i++;
                }
            }
        }

        // Ensure we have at least 30 findings
        while ($i < 30) {
            Finding::create([
                'audit_id' => $audits->random()->id,
                'title' => "Additional Finding $i",
                'description' => "Description for additional finding $i requiring attention",
                'root_cause' => "Root cause analysis for finding $i",
                'impact' => "Impact assessment for finding $i on university operations",
                'recommendation' => "Recommended corrective action for finding $i",
                'severity' => ['critical', 'high', 'medium', 'low'][array_rand(['critical', 'high', 'medium', 'low'])],
                'status' => ['open', 'in_progress'][array_rand(['open', 'in_progress'])],
                'escalation_level' => 1,
                'created_by' => $auditor->id,
                'assigned_to' => $auditor->id,
            ]);
            $i++;
        }
    }
}
