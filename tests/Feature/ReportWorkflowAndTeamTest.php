<?php

namespace Tests\Feature;

use App\Models\Audit;
use App\Models\Report;
use App\Models\User;
use App\Models\RiskRegister;
use Database\Seeders\UsersTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportWorkflowAndTeamTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $manager;
    protected $auditor;
    protected $audit;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UsersTableSeeder::class);

        $this->admin = User::where('email', 'admin@staff.msu.ac.zw')->first();
        $this->manager = User::where('email', 'auditmanager@staff.msu.ac.zw')->first();
        $this->auditor = User::where('email', 'auditor1@staff.msu.ac.zw')->first();

        $risk = RiskRegister::create([
            'risk_code' => 'RSK-001',
            'title' => 'Financial Inaccuracy Risk',
            'description' => 'Test risk description for financial inaccuracy',
            'category' => 'financial',
            'owner_id' => $this->auditor->id,
            'inherent_risk_score' => 4,
            'residual_risk_score' => 2,
            'status' => 'active',
        ]);

        $this->audit = Audit::create([
            'audit_code' => 'AUD-2026-0099',
            'title' => 'Procurement & Inventory Audit',
            'description' => 'Testing audit workflows',
            'audit_type' => 'internal',
            'priority' => 'high',
            'status' => 'in_progress',
            'planned_start_date' => now()->subDays(10),
            'planned_end_date' => now()->addDays(20),
            'actual_start_date' => now()->subDays(10),
            'created_by' => $this->auditor->id,
        ]);

        $this->audit->risks()->attach($risk->id);
    }

    public function test_can_assign_and_display_team_members_in_audit_universe(): void
    {
        $response = $this->actingAs($this->admin)->post(route('audits.assign-team', $this->audit), [
            'team_members' => [$this->auditor->id, $this->manager->id],
        ]);

        $response->assertSessionHas('success');
        $this->assertCount(2, $this->audit->fresh()->teamMembers);

        $indexResponse = $this->actingAs($this->admin)->get(route('audits.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee($this->auditor->name);
    }

    public function test_full_report_review_and_approval_workflow(): void
    {
        // 1. Audit team prepares Draft Report
        $draftResponse = $this->actingAs($this->auditor)->post(route('reports.store'), [
            'audit_id' => $this->audit->id,
            'title' => 'Official Draft Report on Procurement',
            'executive_summary' => 'Key observations were noted during audit testing.',
            'scope' => 'Audit covered all transactions in Q1.',
            'comments' => 'Initial transmittal comments.',
            'draft_issue_date' => now()->toDateString(),
            'submit_for_review' => 0,
        ]);

        $report = Report::where('audit_id', $this->audit->id)->first();
        $this->assertNotNull($report);
        $this->assertEquals('draft', $report->status);
        $this->assertNotNull($report->draft_issue_date);

        // 2. Submit to Senior Internal Auditor
        $submitSenior = $this->actingAs($this->auditor)->post(route('reports.submit-senior', $report), [
            'draft_issue_date' => now()->toDateString(),
            'note' => 'Please review and forward to Chief Auditor.',
        ]);

        $report->refresh();
        $this->assertEquals('pending_senior_review', $report->status);

        // 3. Senior Auditor returns for corrections
        $returnResponse = $this->actingAs($this->auditor)->post(route('reports.return-revision', $report), [
            'comments' => 'Please elaborate on Section 3 scope sample size.',
        ]);

        $report->refresh();
        $this->assertEquals('returned_for_revision', $report->status);
        $this->assertCount(2, $report->review_notes);

        // 4. Audit team addresses correction and resubmits
        $toggleResponse = $this->actingAs($this->auditor)->post(route('reports.toggle-comment', [$report, 1]));
        $this->actingAs($this->auditor)->post(route('reports.submit-senior', $report));
        $report->refresh();
        $this->assertEquals('pending_senior_review', $report->status);

        // 5. Senior Auditor reviews and forwards to Chief Auditor
        $seniorSignOff = $this->actingAs($this->manager)->post(route('reports.submit-chief', $report), [
            'senior_comments' => 'All quality criteria met. Recommended for Chief approval.',
        ]);

        $report->refresh();
        $this->assertEquals('pending_chief_approval', $report->status);
        $this->assertEquals($this->manager->id, $report->senior_reviewer_id);

        // 6. Chief Auditor approves and issues Final Report
        $finalIssueDate = now()->toDateString();
        $chiefIssue = $this->actingAs($this->admin)->post(route('reports.issue', $report), [
            'final_issue_date' => $finalIssueDate,
            'issuance_notes' => 'Approved and officially released.',
        ]);

        $report->refresh();
        $this->assertEquals('final_issued', $report->status);
        $this->assertNotNull($report->final_issue_date);
        $this->assertEquals('completed', $this->audit->fresh()->status);

        // 7. Verify Reports index and PDF download
        $reportsIndex = $this->actingAs($this->admin)->get(route('reports.index'));
        $reportsIndex->assertStatus(200);
        $reportsIndex->assertSee('Final Report Issued');

        $pdfResponse = $this->actingAs($this->admin)->get(route('reports.download', $this->audit));
        $pdfResponse->assertStatus(200);
        $pdfResponse->assertHeader('content-type', 'application/pdf');
    }
}
