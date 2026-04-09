<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Audit Report - {{ $audit->audit_code }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #333; }
        .header { background: #004ea1; color: white; padding: 20px 30px; }
        .header h1 { font-size: 18px; margin-bottom: 3px; }
        .header p { font-size: 10px; color: #ffcc00; }
        .gold-bar { background: #ffcc00; height: 4px; }
        .content { padding: 30px; }
        .section { margin-bottom: 20px; }
        .section-title { font-size: 13px; font-weight: bold; color: #004ea1; border-bottom: 2px solid #004ea1; padding-bottom: 5px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f5f5f5; text-align: left; padding: 6px 8px; font-size: 10px; text-transform: uppercase; color: #666; border-bottom: 1px solid #ddd; }
        td { padding: 6px 8px; border-bottom: 1px solid #eee; font-size: 11px; }
        .meta-grid { display: table; width: 100%; }
        .meta-row { display: table-row; }
        .meta-label { display: table-cell; width: 120px; font-weight: bold; color: #666; padding: 4px 0; font-size: 10px; }
        .meta-value { display: table-cell; padding: 4px 0; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 9px; font-weight: bold; }
        .badge-high { background: #fff3cd; color: #856404; }
        .badge-critical { background: #f8d7da; color: #721c24; }
        .badge-medium { background: #fff3cd; color: #856404; }
        .badge-low { background: #d4edda; color: #155724; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 8px; color: #999; padding: 10px; border-top: 1px solid #eee; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="header">
        <h1>MIDLANDS STATE UNIVERSITY</h1>
        <p>INTERNAL AUDIT DEPARTMENT — OFFICIAL REPORT</p>
    </div>
    <div class="gold-bar"></div>

    <div class="content">
        <div class="section">
            <div class="section-title">Audit Engagement Details</div>
            <div class="meta-grid">
                <div class="meta-row"><div class="meta-label">Audit Code:</div><div class="meta-value">{{ $audit->audit_code }}</div></div>
                <div class="meta-row"><div class="meta-label">Title:</div><div class="meta-value"><strong>{{ $audit->title }}</strong></div></div>
                <div class="meta-row"><div class="meta-label">Type:</div><div class="meta-value">{{ ucfirst($audit->audit_type) }}</div></div>
                <div class="meta-row"><div class="meta-label">Priority:</div><div class="meta-value">{{ ucfirst($audit->priority) }}</div></div>
                <div class="meta-row"><div class="meta-label">Status:</div><div class="meta-value">{{ ucwords(str_replace('_', ' ', $audit->status)) }}</div></div>
                <div class="meta-row"><div class="meta-label">Created By:</div><div class="meta-value">{{ $audit->creator?->name }}</div></div>
                <div class="meta-row"><div class="meta-label">Approved By:</div><div class="meta-value">{{ $audit->approver?->name ?? '—' }}</div></div>
                <div class="meta-row"><div class="meta-label">Planned Period:</div><div class="meta-value">{{ $audit->planned_start_date->format('d M Y') }} — {{ $audit->planned_end_date->format('d M Y') }}</div></div>
                @if($audit->actual_start_date)<div class="meta-row"><div class="meta-label">Actual Period:</div><div class="meta-value">{{ $audit->actual_start_date->format('d M Y') }} — {{ $audit->actual_end_date?->format('d M Y') ?? 'Ongoing' }}</div></div>@endif
            </div>
        </div>

        <div class="section">
            <div class="section-title">Description</div>
            <p>{{ $audit->description }}</p>
        </div>

        <div class="section">
            <div class="section-title">Linked Risks ({{ $audit->risks->count() }})</div>
            <table>
                <thead><tr><th>Code</th><th>Title</th><th>Category</th><th>Inherent</th><th>Residual</th></tr></thead>
                <tbody>
                    @foreach($audit->risks as $risk)
                    <tr><td>{{ $risk->risk_code }}</td><td>{{ $risk->title }}</td><td>{{ ucfirst($risk->category) }}</td><td>{{ $risk->inherent_risk_score }}/5</td><td>{{ $risk->residual_risk_score }}/5</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($audit->findings->count())
        <div class="section">
            <div class="section-title">Audit Findings ({{ $audit->findings->count() }})</div>
            @foreach($audit->findings as $i => $finding)
            <div style="margin-bottom: 15px; padding: 10px; background: #f9f9f9; border-left: 3px solid #004ea1;">
                <p style="font-weight: bold; margin-bottom: 5px;">{{ $i + 1 }}. {{ $finding->title }}
                    <span class="badge badge-{{ $finding->severity }}">{{ ucfirst($finding->severity) }}</span>
                </p>
                <p><strong>Description:</strong> {{ $finding->description }}</p>
                <p><strong>Root Cause:</strong> {{ $finding->root_cause }}</p>
                <p><strong>Impact:</strong> {{ $finding->impact }}</p>
                <p><strong>Recommendation:</strong> {{ $finding->recommendation }}</p>
                <p><strong>Status:</strong> {{ ucwords(str_replace('_', ' ', $finding->status)) }} | <strong>Assigned:</strong> {{ $finding->assignee?->name ?? 'Unassigned' }}</p>

                @if($finding->actionItems->count())
                <p style="margin-top: 5px; font-weight: bold;">Action Items:</p>
                <table>
                    <thead><tr><th>Description</th><th>Assigned To</th><th>Due Date</th><th>Status</th></tr></thead>
                    <tbody>
                        @foreach($finding->actionItems as $action)
                        <tr><td>{{ $action->action_description }}</td><td>{{ $action->assignee?->name }}</td><td>{{ $action->due_date->format('d M Y') }}</td><td>{{ ucfirst($action->status) }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <div class="footer">
        Midlands State University — Internal Audit Report — Generated {{ now()->format('d M Y H:i') }} — CONFIDENTIAL
    </div>
</body>
</html>

