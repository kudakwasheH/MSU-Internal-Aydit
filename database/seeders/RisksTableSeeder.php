<?php

namespace Database\Seeders;

use App\Models\RiskRegister;
use App\Models\User;
use Illuminate\Database\Seeder;

class RisksTableSeeder extends Seeder
{
    public function run(): void
    {
        $riskOfficer = User::where('email', 'riskofficer@msu.ac.zw')->first();
        if (!$riskOfficer) return;

        $risks = [
            [
                'risk_code' => 'RISK-2024-0001', 
                'title' => 'Financial Misstatement Risk', 
                'description' => 'Risk of material misstatements in financial reporting due to inadequate controls', 
                'category' => 'financial', 
                'inherent_likelihood' => 5, 'inherent_impact' => 5,
                'residual_likelihood' => 3, 'residual_impact' => 4
            ],
            [
                'risk_code' => 'RISK-2024-0002', 
                'title' => 'Data Security Breach', 
                'description' => 'Potential unauthorized access to sensitive student and staff data', 
                'category' => 'it', 
                'inherent_likelihood' => 5, 'inherent_impact' => 5,
                'residual_likelihood' => 4, 'residual_impact' => 5
            ],
            [
                'risk_code' => 'RISK-2024-0003', 
                'title' => 'Regulatory Compliance Violation', 
                'description' => 'Non-compliance with ZIMCHE regulations and government policies', 
                'category' => 'compliance', 
                'inherent_likelihood' => 4, 'inherent_impact' => 4,
                'residual_likelihood' => 2, 'residual_impact' => 3
            ],
            [
                'risk_code' => 'RISK-2024-0004', 
                'title' => 'Procurement Fraud', 
                'description' => 'Risk of fraudulent procurement practices and collusion with vendors', 
                'category' => 'operational', 
                'inherent_likelihood' => 4, 'inherent_impact' => 4,
                'residual_likelihood' => 3, 'residual_impact' => 4
            ],
            [
                'risk_code' => 'RISK-2024-0005', 
                'title' => 'Strategic Misalignment', 
                'description' => 'Projects not aligned with university strategic plan 2023-2028', 
                'category' => 'strategic', 
                'inherent_likelihood' => 4, 'inherent_impact' => 3,
                'residual_likelihood' => 3, 'residual_impact' => 3
            ],
            [
                'risk_code' => 'RISK-2024-0006', 
                'title' => 'Student Records Integrity', 
                'description' => 'Unauthorized modification of student academic records', 
                'category' => 'it', 
                'inherent_likelihood' => 5, 'inherent_impact' => 4,
                'residual_likelihood' => 2, 'residual_impact' => 3
            ],
            [
                'risk_code' => 'RISK-2024-0007', 
                'title' => 'Budget Overrun Risk', 
                'description' => 'Departments exceeding approved budgets without authorization', 
                'category' => 'financial', 
                'inherent_likelihood' => 3, 'inherent_impact' => 4,
                'residual_likelihood' => 2, 'residual_impact' => 3
            ],
            [
                'risk_code' => 'RISK-2024-0008', 
                'title' => 'Asset Mismanagement', 
                'description' => 'Inadequate tracking and maintenance of university assets', 
                'category' => 'operational', 
                'inherent_likelihood' => 3, 'inherent_impact' => 3,
                'residual_likelihood' => 2, 'residual_impact' => 2
            ],
            [
                'risk_code' => 'RISK-2024-0009', 
                'title' => 'Staff Competency Gaps', 
                'description' => 'Insufficient skills and training in critical positions', 
                'category' => 'strategic', 
                'inherent_likelihood' => 3, 'inherent_impact' => 3,
                'residual_likelihood' => 2, 'residual_impact' => 2
            ],
            [
                'risk_code' => 'RISK-2024-0010', 
                'title' => 'Revenue Collection Risk', 
                'description' => 'Incomplete or delayed collection of tuition and other fees', 
                'category' => 'financial', 
                'inherent_likelihood' => 4, 'inherent_impact' => 4,
                'residual_likelihood' => 3, 'residual_impact' => 3
            ],
        ];

        foreach ($risks as $risk) {
            $risk['owner_id'] = $riskOfficer->id;
            $risk['status'] = 'active';
            RiskRegister::create($risk);
        }
    }
}
