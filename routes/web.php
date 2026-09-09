<?php

use App\Http\Controllers\AuditController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FindingController;
use App\Http\Controllers\RiskController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ActionItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WorkingPaperController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    // Role-specific Dashboards
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/auditor', [DashboardController::class, 'auditor'])->name('dashboard.auditor')->middleware('role:Auditor|System Admin');
    Route::get('/dashboard/risk-officer', [DashboardController::class, 'riskOfficer'])->name('dashboard.risk-officer')->middleware('role:Risk Officer|System Admin');
    Route::get('/dashboard/committee', [DashboardController::class, 'committee'])->name('dashboard.committee')->middleware('role:Audit Committee|System Admin');
    Route::get('/dashboard/executive', [DashboardController::class, 'executive'])->name('dashboard.executive')->middleware('role:Executive Management|Council / Board|System Admin');

    // Resources & specific actions locked by Permissions
    Route::middleware('permission:view audits')->group(function () {
        Route::resource('audits', AuditController::class);
        Route::post('/audits/{audit}/assign-team', [AuditController::class, 'assignTeam'])->name('audits.assign-team')->middleware('permission:edit audits');
        Route::resource('working-papers', WorkingPaperController::class);
        Route::get('/working-papers/{workingPaper}/download', [WorkingPaperController::class, 'download'])->name('working-papers.download');
        Route::post('/audits/{audit}/approve', [AuditController::class, 'approve'])->name('audits.approve')->middleware('permission:approve audits');
        Route::post('/audits/{audit}/submit', [AuditController::class, 'submit'])->name('audits.submit')->middleware('permission:edit audits');
    });

    Route::middleware('permission:view risks')->group(function () {
        Route::resource('risks', RiskController::class)->only(['index', 'show']);
        Route::post('/risks/sync', [RiskController::class, 'sync'])->name('risks.sync')->middleware('permission:edit risks');
        Route::get('/risk-heatmap', [RiskController::class, 'heatmap'])->name('risks.heatmap');
    });

    Route::middleware('permission:view findings')->group(function () {
        Route::resource('findings', FindingController::class);
        Route::post('/findings/{finding}/escalate', [FindingController::class, 'escalate'])->name('findings.escalate')->middleware('permission:escalate findings');
    });

    Route::middleware('permission:view action-items')->group(function () {
        Route::resource('action-items', ActionItemController::class);
        Route::post('/action-items/{actionItem}/complete', [ActionItemController::class, 'complete'])->name('action-items.complete')->middleware('permission:complete action-items');
    });

    Route::middleware('permission:view reports')->group(function () {
        Route::get('/reports/dashboard', [ReportController::class, 'generate'])->name('reports.generate')->middleware('permission:generate reports');
        Route::get('/reports/meeting-pack', [ReportController::class, 'meetingPack'])->name('reports.meeting-pack');
        Route::get('/reports/rolling-plan', [ReportController::class, 'rollingPlan'])->name('reports.rolling-plan');
        Route::get('/reports/download/{audit}', [ReportController::class, 'download'])->name('reports.download');
        Route::get('/system-manual/download', [ReportController::class, 'downloadSystemManual'])->name('system-manual.download');
        Route::get('/audit-logs', [ReportController::class, 'auditLogs'])->name('audit-logs.index')->middleware('permission:view audit-logs');

        Route::resource('reports', ReportController::class)->except(['destroy']);
        Route::post('/reports/{report}/submit-senior', [ReportController::class, 'submitForSeniorReview'])->name('reports.submit-senior');
        Route::post('/reports/{report}/submit-chief', [ReportController::class, 'submitForChiefApproval'])->name('reports.submit-chief');
        Route::post('/reports/{report}/approve-chief', [ReportController::class, 'approveChief'])->name('reports.approve-chief');
        Route::post('/reports/{report}/issue', [ReportController::class, 'issueFinalReport'])->name('reports.issue');
        Route::post('/reports/{report}/return-revision', [ReportController::class, 'returnForRevision'])->name('reports.return-revision');
        Route::post('/reports/{report}/reject', [ReportController::class, 'rejectReport'])->name('reports.reject');
        Route::post('/reports/{report}/add-comment', [ReportController::class, 'addComment'])->name('reports.add-comment');
        Route::post('/reports/{report}/toggle-comment/{index}', [ReportController::class, 'toggleCommentStatus'])->name('reports.toggle-comment');
        Route::post('/reports/{report}/update-dates', [ReportController::class, 'updateDates'])->name('reports.update-dates');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    Route::middleware('permission:manage users')->group(function () {
        Route::resource('users', UserController::class);
    });
});

require __DIR__.'/auth.php';
