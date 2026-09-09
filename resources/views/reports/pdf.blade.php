<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Audit Report - {{ $audit->audit_code }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #333; line-height: 1.4; }
        .header { background: #004ea1; color: white; padding: 18px 30px; }
        .header h1 { font-size: 16px; margin-bottom: 3px; }
        .header p { font-size: 9px; color: #ffcc00; font-weight: bold; }
        .gold-bar { background: #ffcc00; height: 3px; }
        .content { padding: 25px 30px; }
        .section { margin-bottom: 18px; }
        .section-title { font-size: 11px; font-weight: bold; color: #004ea1; border-bottom: 1.5px solid #004ea1; padding-bottom: 4px; margin-bottom: 8px; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        th { background: #f0f4f8; text-align: left; padding: 5px 6px; font-size: 9px; text-transform: uppercase; color: #333; border-bottom: 1px solid #ccd; }
        td { padding: 5px 6px; border-bottom: 1px solid #eee; font-size: 9.5px; }
        .meta-grid { display: table; width: 100%; }
        .meta-row { display: table-row; }
        .meta-label { display: table-cell; width: 130px; font-weight: bold; color: #555; padding: 3px 0; font-size: 9.5px; }
        .meta-value { display: table-cell; padding: 3px 0; font-size: 9.5px; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 8px; font-weight: bold; text-transform: uppercase; }
        .badge-high, .badge-critical { background: #f8d7da; color: #721c24; }
        .badge-medium { background: #fff3cd; color: #856404; }
        .badge-low { background: #d4edda; color: #155724; }
        .status-badge { display: inline-block; padding: 3px 8px; font-size: 9px; font-weight: bold; border-radius: 4px; text-transform: uppercase; }
        .status-final { background: #d4edda; color: #155724; }
        .status-draft { background: #fff3cd; color: #856404; }
        .signoff-box { display: table; width: 100%; margin-top: 15px; border-top: 1px solid #ddd; padding-top: 10px; }
        .signoff-cell { display: table-cell; width: 33.3%; vertical-align: top; padding-right: 10px; }
        .signoff-title { font-size: 9px; font-weight: bold; color: #666; text-transform: uppercase; }
        .signoff-name { font-size: 10px; font-weight: bold; color: #004ea1; margin-top: 2px; }
        .signoff-date { font-size: 8.5px; color: #888; margin-top: 1px; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 8px; color: #999; padding: 8px; border-top: 1px solid #eee; }
        .page-break { page-break-after: always; }
        .box { background: #f9fbfd; border: 1px solid #e2e8f0; padding: 8px; border-radius: 4px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%; border: none; margin: 0;">
            <tr>
                <td style="border: none; padding: 0;">
                    <h1>MIDLANDS STATE UNIVERSITY</h1>
                    <p>INTERNAL AUDIT DIRECTORATE — OFFICIAL REPORT</p>
                </td>
                <td style="border: none; padding: 0; text-align: right;">
                    @if($audit->report && $audit->report->status === 'final_issued')
                        <span class="status-badge status-final">OFFICIAL FINAL REPORT</span>
                    @else
                        <span class="status-badge status-draft">DRAFT REPORT FOR REVIEW</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>
    <div class="gold-bar"></div>

    <div class="content">
        {{-- Section 1: Engagement & Report Metadata --}}
        <div class="section">
            <div class="section-title">1. Audit Engagement & Report Overview</div>
            <div class="meta-grid">
                <div class="meta-row"><div class="meta-label">Audit Engagement:</div><div class="meta-value"><strong>{{ $audit->audit_code }} — {{ $audit->title }}</strong></div></div>
                <div class="meta-row"><div class="meta-label">Engagement Type:</div><div class="meta-value">{{ ucfirst($audit->audit_type) }} Audit | Priority: {{ ucfirst($audit->priority) }}</div></div>
                <div class="meta-row"><div class="meta-label">Audit Period:</div><div class="meta-value">{{ $audit->planned_start_date->format('d M Y') }} — {{ $audit->planned_end_date->format('d M Y') }}</div></div>
                <div class="meta-row"><div class="meta-label">Draft Issue Date:</div><div class="meta-value">{{ $audit->report?->draft_issue_date ? $audit->report->draft_issue_date->format('d F Y') : '—' }}</div></div>
                <div class="meta-row"><div class="meta-label">Final Issue Date:</div><div class="meta-value">{{ $audit->report?->final_issue_date ? $audit->report->final_issue_date->format('d F Y') : ($audit->report?->status === 'final_issued' ? now()->format('d F Y') : 'Pending Final Issuance') }}</div></div>
                <div class="meta-row">
                    <div class="meta-label">Audit Team Members:</div>
                    <div class="meta-value">
                        @if($audit->teamMembers->count())
                            {{ $audit->teamMembers->pluck('name')->join(', ') }}
                        @else
                            {{ $audit->creator?->name }} (Lead)
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 2: Executive Summary --}}
        @if($audit->report && $audit->report->executive_summary)
        <div class="section">
            <div class="section-title">2. Executive Summary & Key Findings</div>
            <div class="box">
                {!! nl2br(e($audit->report->executive_summary)) !!}
            </div>
        </div>
        @else
        <div class="section">
            <div class="section-title">2. Engagement Objective & Background</div>
            <p>{{ $audit->description }}</p>
        </div>
        @endif

        {{-- Section 3: Scope & Methodology --}}
        @if($audit->report && $audit->report->scope)
        <div class="section">
            <div class="section-title">3. Scope, Limitations & Methodology</div>
            <p>{!! nl2br(e($audit->report->scope)) !!}</p>
        </div>
        @endif

        {{-- Section 4: Linked Risk Universe --}}
        <div class="section">
            <div class="section-title">4. Risk Assessment Profile ({{ $audit->risks->count() }} Linked Risks)</div>
            <table>
                <thead>
                    <tr><th>Risk Code</th><th>Risk Title</th><th>Category</th><th>Inherent Score</th><th>Residual Score</th></tr>
                </thead>
                <tbody>
                    @foreach($audit->risks as $risk)
                    <tr>
                        <td><strong>{{ $risk->risk_code }}</strong></td>
                        <td>{{ $risk->title }}</td>
                        <td>{{ ucfirst($risk->category) }}</td>
                        <td>{{ $risk->inherent_risk_score }}/5</td>
                        <td>{{ $risk->residual_risk_score }}/5</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Section 5: Audit Findings & Deficiencies --}}
        @if($audit->findings->count())
        <div class="section">
            <div class="section-title">5. Audit Findings & Corrective Action Plans ({{ $audit->findings->count() }})</div>
            @foreach($audit->findings as $i => $finding)
            <div style="margin-bottom: 12px; padding: 8px 10px; background: #fafafa; border-left: 3px solid #004ea1;">
                <p style="font-weight: bold; margin-bottom: 4px;">
                    {{ $i + 1 }}. {{ $finding->title }}
                    <span class="badge badge-{{ $finding->severity }}">{{ ucfirst($finding->severity) }}</span>
                </p>
                <p><strong>Observation & Root Cause:</strong> {{ $finding->description }} ({{ $finding->root_cause }})</p>
                <p><strong>Impact / Business Risk:</strong> {{ $finding->impact }}</p>
                <p><strong>Audit Recommendation:</strong> {{ $finding->recommendation }}</p>
                <p><strong>Responsible Owner:</strong> {{ $finding->assignee?->name ?? 'Management' }} | <strong>Status:</strong> {{ ucwords(str_replace('_', ' ', $finding->status)) }}</p>

                @if($finding->actionItems->count())
                <table style="margin-top: 6px;">
                    <thead><tr><th>Action Plan</th><th>Assigned To</th><th>Target Due Date</th><th>Status</th></tr></thead>
                    <tbody>
                        @foreach($finding->actionItems as $action)
                        <tr>
                            <td>{{ $action->action_description }}</td>
                            <td>{{ $action->assignee?->name }}</td>
                            <td>{{ $action->due_date->format('d M Y') }}</td>
                            <td>{{ ucfirst($action->status) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
            @endforeach
        </div>
        @endif

        {{-- Section 6: Governance & Sign-off Block --}}
        <div class="section">
            <div class="section-title">6. Quality Review & Official Authorizations</div>
            <div class="signoff-box">
                <div class="signoff-cell">
                    <div class="signoff-title">Prepared By (Audit Team):</div>
                    <div class="signoff-name">{{ $audit->report?->preparer?->name ?? $audit->creator?->name }}</div>
                    <div class="signoff-date">Date: {{ $audit->report?->draft_issue_date?->format('d M Y') ?? date('d M Y') }}</div>
                </div>

                <div class="signoff-cell">
                    <div class="signoff-title">Senior Auditor Review:</div>
                    <div class="signoff-name">{{ $audit->report?->seniorReviewer?->name ?? 'Verified Quality Standard' }}</div>
                    <div class="signoff-date">Status: {{ $audit->report?->senior_reviewer_id ? 'Reviewed & Endorsed' : 'Pending' }}</div>
                </div>

                <div class="signoff-cell">
                    <div class="signoff-title">Chief Internal Auditor:</div>
                    <div class="signoff-name">{{ $audit->report?->chiefApprover?->name ?? $audit->approver?->name ?? 'Chief Internal Auditor' }}</div>
                    <div class="signoff-date">Date: {{ $audit->report?->final_issue_date?->format('d M Y') ?? ($audit->report?->status === 'final_issued' ? date('d M Y') : 'Pending Final Issuance') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        Midlands State University &middot; Internal Audit Directorate &middot; {{ $audit->audit_code }} &middot; Confidential
    </div>
</body>
</html>
