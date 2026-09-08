<?php

namespace App\Http\Controllers;

use App\Models\RiskRegister;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiskController extends Controller
{
    public function index(Request $request, \App\Services\RiskApiService $apiService)
    {
        $risks = $apiService->fetchRisks();

        // Convert to collection for easier filtering if needed
        $risks = collect($risks);

        if ($request->filled('category')) {
            $risks = $risks->where('category', $request->category);
        }
        if ($request->filled('status')) {
            $risks = $risks->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $risks = $risks->filter(function($risk) use ($search) {
                return str_contains(strtolower($risk['title'] ?? ''), $search) || 
                       str_contains(strtolower($risk['risk_code'] ?? ''), $search) ||
                       str_contains((string)($risk['id'] ?? ''), $search);
            });
        }

        // Map API data to RiskRegister model instances (without persisting)
        $risks = $risks->map(function($apiRisk) {
            // Find owner if possible, otherwise mock it
            $owner = null;
            if (!empty($apiRisk['owner_email'])) {
                $owner = User::where('email', $apiRisk['owner_email'])->first();
            }

            $risk = new RiskRegister($apiRisk);
            $risk->id = $apiRisk['id'] ?? null; 
            $risk->risk_code = $apiRisk['risk_code'] ?? ($apiRisk['id'] ?? 'UNKNOWN');
            $risk->setRelation('owner', $owner);
            
            // Set high-fidelity mock data if not in API
            $risk->kra_at_risk = $apiRisk['kra_at_risk'] ?? 'Governance, Leadership and Culture';
            $risk->cause = $apiRisk['cause'] ?? 'Lack of monitoring';
            $risk->consequence = $apiRisk['consequence'] ?? 'Lack of governance and accountability';
            $risk->last_reviewed_at = $apiRisk['last_reviewed_at'] ?? date('Y-m-d');

            // Recalculate virtual attributes if not provided by API
            if (!isset($apiRisk['inherent_risk_score'])) {
                $risk->inherent_risk_score = (int)($apiRisk['inherent_likelihood'] ?? 0) * (int)($apiRisk['inherent_impact'] ?? 0);
            }
            if (!isset($apiRisk['residual_risk_score'])) {
                $risk->residual_risk_score = (int)($apiRisk['residual_likelihood'] ?? 0) * (int)($apiRisk['residual_impact'] ?? 0);
            }

            return $risk;
        });

        // Manual pagination
        $perPage = 10;
        $page = $request->get('page', 1);
        $paginatedRisks = new \Illuminate\Pagination\LengthAwarePaginator(
            $risks->forPage($page, $perPage),
            $risks->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $risks = $paginatedRisks;
        return view('risks.index', compact('risks'));
    }

    public function sync()
    {
        try {
            $exitCode = \Illuminate\Support\Facades\Artisan::call('risk:sync');
            $output = \Illuminate\Support\Facades\Artisan::output();
            
            if ($exitCode === 0) {
                return redirect()->route('risks.index')->with('success', 'Risk register synced successfully from API.');
            } else {
                return redirect()->route('risks.index')->with('error', 'Failed to sync risks: ' . $output);
            }
        } catch (\Exception $e) {
            return redirect()->route('risks.index')->with('error', 'Error during sync: ' . $e->getMessage());
        }
    }

    public function show($id, \App\Services\RiskApiService $apiService)
    {
        $apiRisks = $apiService->fetchRisks();
        $apiRisk = collect($apiRisks)->firstWhere('id', $id);

        if (!$apiRisk) {
            // Try searching by risk_code if id doesn't match
            $apiRisk = collect($apiRisks)->firstWhere('risk_code', $id);
        }

        if (!$apiRisk) {
            abort(404, 'Risk not found in master register.');
        }

        $risk = new RiskRegister($apiRisk);
        $risk->id = $apiRisk['id'] ?? null;

        // Find owner
        if (!empty($apiRisk['owner_email'])) {
            $owner = User::where('email', $apiRisk['owner_email'])->first();
            $risk->setRelation('owner', $owner);
        }

        // Set high-fidelity mock data if not in API
        $risk->kra_at_risk = $apiRisk['kra_at_risk'] ?? 'Governance, Leadership and Culture';
        $risk->cause = $apiRisk['cause'] ?? 'Lack of monitoring';
        $risk->consequence = $apiRisk['consequence'] ?? 'Lack of governance and accountability';
        $risk->last_reviewed_at = $apiRisk['last_reviewed_at'] ?? date('Y-m-d');

        // Recalculate virtual attributes
        $risk->inherent_risk_score = (int)($apiRisk['inherent_likelihood'] ?? 0) * (int)($apiRisk['inherent_impact'] ?? 0);
        $risk->residual_risk_score = (int)($apiRisk['residual_likelihood'] ?? 0) * (int)($apiRisk['residual_impact'] ?? 0);

        // Load audits (this still requires the database link)
        $dbRisk = RiskRegister::where('risk_code', $risk->risk_code)->first();
        if ($dbRisk) {
            $risk->setRelation('audits', $dbRisk->audits);
        } else {
            $risk->setRelation('audits', collect());
        }

        return view('risks.show', compact('risk'));
    }

    public function heatmap(\App\Services\RiskApiService $apiService)
    {
        $apiRisks = $apiService->fetchRisks();
        $risks = collect($apiRisks)->where('status', 'active')->map(function($apiRisk) {
            $risk = new RiskRegister($apiRisk);
            $risk->inherent_risk_score = ($apiRisk['inherent_likelihood'] ?? 0) * ($apiRisk['inherent_impact'] ?? 0);
            $risk->residual_risk_score = ($apiRisk['residual_likelihood'] ?? 0) * ($apiRisk['residual_impact'] ?? 0);
            return $risk;
        });
        
        return view('risks.heatmap', compact('risks'));
    }
}
