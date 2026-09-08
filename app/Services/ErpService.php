<?php

namespace App\Services;

class ErpService
{
    /**
     * Fetch budget codes from the ERP system.
     *
     * @return array
     */
    public function fetchBudgetCodes()
    {
        // Mocking ERP API response
        return [
            ['code' => 'BGT-2026-ADM', 'description' => 'Administration & General'],
            ['code' => 'BGT-2026-ICT', 'description' => 'ICT Infrastructure'],
            ['code' => 'BGT-2026-RES', 'description' => 'Research & Innovation'],
            ['code' => 'BGT-2026-CAP', 'description' => 'Capital Expenditure'],
            ['code' => 'BGT-2026-OPS', 'description' => 'Operational Expenses'],
        ];
    }

    /**
     * Fetch compliance frameworks/references.
     *
     * @return array
     */
    public function fetchComplianceReferences()
    {
        return [
            ['ref' => 'ISO-27001', 'title' => 'Information Security Management'],
            ['ref' => 'COBIT-2019', 'title' => 'Governance of Enterprise IT'],
            ['ref' => 'PFMA-ZIM', 'title' => 'Public Finance Management Act'],
            ['ref' => 'MSU-STAT-1', 'title' => 'MSU Statutes & Regulations'],
        ];
    }
}
