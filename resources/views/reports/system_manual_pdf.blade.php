<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>MSU Internal Audit System — Architecture, Operation & Workflow Manual</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9.5px; color: #2d3748; line-height: 1.45; }
        .header { background: #004ea1; color: white; padding: 20px 30px; }
        .header h1 { font-size: 16px; font-weight: bold; margin-bottom: 2px; }
        .header p { font-size: 9px; color: #ffcc00; font-weight: bold; letter-spacing: 0.5px; }
        .gold-bar { background: #ffcc00; height: 3.5px; width: 100%; }
        .content { padding: 25px 30px; }
        .section { margin-bottom: 20px; }
        .section-title { font-size: 12px; font-weight: bold; color: #004ea1; border-bottom: 1.5px solid #004ea1; padding-bottom: 4px; margin-bottom: 10px; text-transform: uppercase; }
        .sub-title { font-size: 10.5px; font-weight: bold; color: #2b6cb0; margin-top: 8px; margin-bottom: 4px; }
        p { margin-bottom: 6px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; margin-top: 4px; }
        th { background: #f0f4f8; text-align: left; padding: 6px 8px; font-size: 8.5px; text-transform: uppercase; color: #2d3748; border: 1px solid #cbd5e0; font-weight: bold; }
        td { padding: 5.5px 8px; border: 1px solid #e2e8f0; font-size: 9px; vertical-align: top; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 8px; font-weight: bold; text-transform: uppercase; }
        .badge-blue { background: #ebf8ff; color: #2b6cb0; border: 1px solid #bee3f8; }
        .badge-purple { background: #faf5ff; color: #6b46c1; border: 1px solid #e9d8fd; }
        .badge-green { background: #f0fff4; color: #276749; border: 1px solid #c6f6d5; }
        .badge-amber { background: #fffaf0; color: #9c4221; border: 1px solid #feebc8; }
        .box { background: #f7fafc; border: 1px solid #e2e8f0; border-left: 3.5px solid #004ea1; padding: 8px 10px; border-radius: 3px; margin-bottom: 8px; }
        .level-box { background: #ffffff; border: 1px solid #cbd5e0; border-radius: 4px; padding: 8px 10px; margin-bottom: 10px; }
        .level-header { font-weight: bold; font-size: 10px; color: #004ea1; border-bottom: 1px solid #edf2f7; padding-bottom: 3px; margin-bottom: 5px; }
        .step-list { margin-left: 15px; margin-bottom: 6px; }
        .step-list li { margin-bottom: 3px; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 8px; color: #718096; padding: 8px; border-top: 1px solid #e2e8f0; background: #fff; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <table style="width: 100%; border: none; margin: 0;">
            <tr>
                <td style="border: none; padding: 0;">
                    <h1>MIDLANDS STATE UNIVERSITY</h1>
                    <p>INTERNAL AUDIT DIRECTORATE &middot; SYSTEM OPERATION & WORKFLOW MANUAL</p>
                </td>
                <td style="border: none; padding: 0; text-align: right; vertical-align: middle;">
                    <span style="background: rgba(255,255,255,0.2); padding: 4px 10px; border-radius: 4px; font-weight: bold; font-size: 8.5px; color: #fff;">
                        OFFICIAL USER GUIDE
                    </span>
                </td>
            </tr>
        </table>
    </div>
    <div class="gold-bar"></div>

    <div class="content">
        {{-- Section 1: System Overview --}}
        <div class="section">
            <div class="section-title">1. Executive Overview & System Architecture</div>
            <p>
                The <strong>MSU Internal Audit Management System</strong> is an enterprise governance, risk, and audit execution platform tailored for Midlands State University. Built to uphold the standards of the Institute of Internal Auditors (IIA), the system digitalizes the end-to-end audit lifecycle from strategic annual planning down to finding remediation.
            </p>
            <div class="box">
                <strong>Core Modules & Capabilities:</strong>
                <ul style="margin-left: 15px; margin-top: 3px;">
                    <li><strong>Audit Universe:</strong> Annual rolling audit planning, engagement scheduling, and multi-auditor team allocation.</li>
                    <li><strong>Risk Management:</strong> Integrated Risk Register, Inherent & Residual risk scoring, and interactive 5x5 Heatmap.</li>
                    <li><strong>Working Papers & Fieldwork:</strong> Electronic audit testing papers, methodology templates, and evidentiary document storage.</li>
                    <li><strong>Finding Tracker:</strong> Comprehensive deficiency cataloging, 5-level risk escalation, and root-cause mapping.</li>
                    <li><strong>Action Items:</strong> Management corrective action tracking, automated overdue alerts, and remediation follow-up.</li>
                    <li><strong>Reports Centre:</strong> 4-stage review and approval governance, transmittal tracking, and official PDF generation.</li>
                    <li><strong>Executive Governance:</strong> Role-tailored dashboards for the Vice Chancellor, Audit Committee, and University Council.</li>
                </ul>
            </div>
        </div>

        {{-- Section 2: Approval Level Hierarchy --}}
        <div class="section">
            <div class="section-title">2. Sign-off Authority & Approval Levels (1–5)</div>
            <p>
                The system enforces a dual-layer security model comprising <strong>System Roles</strong> (interface permissions) and <strong>Approval Levels</strong> (hierarchical sign-off authority and delegation limits):
            </p>

            <table>
                <thead>
                    <tr>
                        <th style="width: 14%;">Level</th>
                        <th style="width: 26%;">Position / Tier</th>
                        <th style="width: 60%;">Sign-off Authority & Operational Scope</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="badge badge-blue">Level 1</span></td>
                        <td><strong>Field / Operational Auditor</strong><br><span style="color:#718096; font-size:8px;">Junior Auditor, Audit Assistant</span></td>
                        <td>
                            • Executes test procedures and records fieldwork data.<br>
                            • Prepares working papers and uploads supporting audit evidence.<br>
                            • Drafts initial audit observations, root causes, and draft reports.<br>
                            • <em>Does not have authority to approve plans or issue final reports.</em>
                        </td>
                    </tr>
                    <tr>
                        <td><span class="badge badge-blue">Level 2</span></td>
                        <td><strong>Supervisory Reviewer</strong><br><span style="color:#718096; font-size:8px;">Senior Auditor, Risk Officer</span></td>
                        <td>
                            • Performs first-line quality review on working papers and audit evidence.<br>
                            • Reviews draft audit reports prepared by Level 1 auditors.<br>
                            • Logs required corrections or endorses reports for Chief Auditor approval.<br>
                            • Validates enterprise risk ratings and escalates critical findings.
                        </td>
                    </tr>
                    <tr>
                        <td><span class="badge badge-purple">Level 3</span></td>
                        <td><strong>Management / Chief Auditor</strong><br><span style="color:#718096; font-size:8px;">Audit Manager, Chief Internal Auditor</span></td>
                        <td>
                            • Approves Audit Universe engagements and authorizes audit commencement.<br>
                            • Conducts executive reviews on draft reports and endorses quality compliance.<br>
                            • <strong>Formally authorizes and issues Official Final Audit Reports.</strong><br>
                            • Verifies management remediation and formally closes resolved findings.
                        </td>
                    </tr>
                    <tr>
                        <td><span class="badge badge-green">Level 4</span></td>
                        <td><strong>Executive & Governance</strong><br><span style="color:#718096; font-size:8px;">Audit Committee, Vice Chancellor, Council</span></td>
                        <td>
                            • High-level university governance oversight.<br>
                            • Accesses Executive & Governance Dashboards and Quarterly Meeting Packs.<br>
                            • Evaluates university-wide strategic risk posture, audit cycle times, and unresolved high-risk exposures.
                        </td>
                    </tr>
                    <tr>
                        <td><span class="badge badge-amber">Level 5</span></td>
                        <td><strong>System Administrator</strong><br><span style="color:#718096; font-size:8px;">System Admin, IT Directorate</span></td>
                        <td>
                            • Root configuration, staff account provisioning, and role assignment.<br>
                            • System integration management (Google SSO, ERP budget linkage, Risk sync).<br>
                            • Monitors immutable system audit logs and application health.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="page-break"></div>

        {{-- Section 3: End-to-End Workflow by User Level --}}
        <div class="section">
            <div class="section-title">3. Operational Workflow Guide by Role & Level</div>

            {{-- Level 1 --}}
            <div class="level-box">
                <div class="level-header">Level 1: Operational Field Auditor Workflow</div>
                <ol class="step-list">
                    <li><strong>Assignment:</strong> Receives allocation to an active audit engagement in the <em>Audit Universe</em>.</li>
                    <li><strong>Fieldwork Testing:</strong> Navigates to <em>Working Papers</em>, creates working papers associated with linked university risks, and attaches test evidence files.</li>
                    <li><strong>Deficiency Logging:</strong> Identifies internal control gaps and creates records in the <em>Finding Tracker</em> (noting criteria, condition, cause, effect, and recommendation).</li>
                    <li><strong>Draft Report Preparation:</strong> Upon conclusion of testing, opens <em>Reports Centre &rarr; Prepare New Report</em>, fills the executive summary, methodology, and transmittal comments.</li>
                    <li><strong>Draft Submission:</strong> Submits the draft report to the Senior Internal Auditor (Status becomes <code>pending_senior_review</code>, and the <strong>Draft Issue Date</strong> is captured).</li>
                    <li><strong>Addressing Feedback:</strong> If returned with comments, addresses the requested corrections and resubmits.</li>
                </ol>
            </div>

            {{-- Level 2 --}}
            <div class="level-box">
                <div class="level-header">Level 2: Senior Internal Auditor (Supervisory Review) Workflow</div>
                <ol class="step-list">
                    <li><strong>Quality Review:</strong> Accesses assigned engagements and reviews submitted working papers and test sampling.</li>
                    <li><strong>Report Review Gate:</strong> Opens the draft report in <em>Reports Centre</em>. Evaluates completeness and accuracy.</li>
                    <li><strong>Decision Pathways:</strong>
                        <ul style="margin-left: 15px; margin-top: 2px;">
                            <li><em>If corrections are required:</em> Clicks <strong>"Request Corrections"</strong>, enters specific observations. The report status updates to <code>returned_for_revision</code>.</li>
                            <li><em>If quality standards are met:</em> Clicks <strong>"Endorse & Forward to Chief"</strong> with senior endorsement notes. The report moves to <code>pending_chief_approval</code>.</li>
                        </ul>
                    </li>
                    <li><strong>Finding Escalation:</strong> Evaluates finding risk ratings and triggers formal escalation levels if management response is overdue.</li>
                </ol>
            </div>

            {{-- Level 3 --}}
            <div class="level-box">
                <div class="level-header">Level 3: Audit Manager / Chief Internal Auditor Workflow</div>
                <ol class="step-list">
                    <li><strong>Annual Planning:</strong> Creates audit engagements in the <em>Audit Universe</em>, links ERP budget codes, and defines planned dates.</li>
                    <li><strong>Team Allocation:</strong> Uses the <strong>"Assign Audit Team"</strong> tool to allocate Lead Auditors and team members to engagements.</li>
                    <li><strong>Engagement Authorization:</strong> Reviews engagement charters and approves draft plans (Status: <code>planned &rarr; in_progress</code>).</li>
                    <li><strong>Chief Report Review:</strong> Reviews reports forwarded by Senior Auditors.</li>
                    <li><strong>Final Report Issuance:</strong> Approves the report and clicks <strong>"Authorize & Issue Final Report"</strong> (Captures the <strong>Final Issue Date</strong>, locks the report as <code>final_issued</code>, marks the audit <code>completed</code>, and watermarks the official PDF).</li>
                    <li><strong>Finding Closure:</strong> Reviews verified action items and formally marks findings as resolved/closed.</li>
                </ol>
            </div>

            {{-- Level 4 --}}
            <div class="level-box">
                <div class="level-header">Level 4: Executive Management, Audit Committee & Council Workflow</div>
                <ol class="step-list">
                    <li><strong>Executive Dashboard:</strong> Reviews real-time KPIs covering university risk coverage %, audit cycle times, and finding resolution velocity.</li>
                    <li><strong>Quarterly Meeting Pack:</strong> Downloads pre-compiled executive meeting packs containing summarized finding aging and high-risk exposures.</li>
                    <li><strong>Risk Heatmap Analysis:</strong> Analyzes dynamic risk distribution across faculties, departments, and compliance mandates.</li>
                </ol>
            </div>

            {{-- Level 5 --}}
            <div class="level-box">
                <div class="level-header">Level 5: System Administrator & IT Lead Workflow</div>
                <ol class="step-list">
                    <li><strong>Staff Account Onboarding:</strong> Creates user profiles with mandatory <code>@staff.msu.ac.zw</code> emails, positions, and approval levels (1-5).</li>
                    <li><strong>Google SSO Governance:</strong> Enforces secure passwordless Google Workspace authentication.</li>
                    <li><strong>Audit Trail Inspection:</strong> Reviews immutable system logs in <em>System Logs</em> to trace every login, record creation, update, and deletion.</li>
                </ol>
            </div>
        </div>

        <div class="page-break"></div>

        {{-- Section 4: Lifecycle Diagram & Audit Flowchart --}}
        <div class="section">
            <div class="section-title">4. Comprehensive Audit Engagement Lifecycle</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 18%;">Stage</th>
                        <th style="width: 22%;">Key Activities</th>
                        <th style="width: 25%;">Primary Actors</th>
                        <th style="width: 35%;">System Output / Deliverable</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>1. Planning</strong></td>
                        <td>Engagement creation, risk linkage, ERP budget coding</td>
                        <td>Audit Manager (L3), System Admin (L5)</td>
                        <td>Approved Audit Universe Plan (Code: AUD-YYYY-XXXX)</td>
                    </tr>
                    <tr>
                        <td><strong>2. Allocation</strong></td>
                        <td>Assigning auditors & field team members</td>
                        <td>Chief Auditor / Audit Manager (L3)</td>
                        <td>Engagement Team roster with avatar display</td>
                    </tr>
                    <tr>
                        <td><strong>3. Fieldwork</strong></td>
                        <td>Executing tests, collecting evidence, sampling</td>
                        <td>Auditors / Field Team (L1)</td>
                        <td>Versioned Working Papers & uploaded evidence</td>
                    </tr>
                    <tr>
                        <td><strong>4. Findings</strong></td>
                        <td>Identifying deficiencies & root causes</td>
                        <td>Auditors (L1), Senior Reviewer (L2)</td>
                        <td>Finding records with severity & action items</td>
                    </tr>
                    <tr>
                        <td><strong>5. Draft Report</strong></td>
                        <td>Drafting summary, methodology & initial issue</td>
                        <td>Audit Team Preparer (L1)</td>
                        <td>Draft Report (Captures <strong>Draft Issue Date</strong>)</td>
                    </tr>
                    <tr>
                        <td><strong>6. Senior Review</strong></td>
                        <td>Quality testing, correction logs, endorsement</td>
                        <td>Senior Internal Auditor (L2)</td>
                        <td>Endorsed Report or Correction Tracker items</td>
                    </tr>
                    <tr>
                        <td><strong>7. Chief Approval</strong></td>
                        <td>Executive review, authorization, final issue</td>
                        <td>Chief Internal Auditor (L3)</td>
                        <td><strong>Official Final Report (Final Issue Date)</strong></td>
                    </tr>
                    <tr>
                        <td><strong>8. Remediation</strong></td>
                        <td>Action plan follow-up, evidence review, closure</td>
                        <td>Action Owners, Audit Manager (L3)</td>
                        <td>Closed findings & Governance Analytics</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Section 5: Authentication & Security --}}
        <div class="section">
            <div class="section-title">5. Authentication & Security Policy</div>
            <div class="box">
                <p><strong>1. Single Sign-On (Google Workspace):</strong> All university staff authenticate securely via Google OAuth2 with their official <code>@staff.msu.ac.zw</code> credentials.</p>
                <p><strong>2. Granular Role-Based Access Control (RBAC):</strong> Managed through Spatie permissions to guarantee separation of duties.</p>
                <p><strong>3. Tamper-Proof Audit Trails:</strong> Every critical model action (create, update, delete, approve, issue) is logged with the user ID, timestamp, and before/after state diff.</p>
            </div>
        </div>
    </div>

    <div class="footer">
        Midlands State University &middot; Internal Audit Directorate &middot; System Operations Manual &middot; Confidential
    </div>
</body>
</html>
