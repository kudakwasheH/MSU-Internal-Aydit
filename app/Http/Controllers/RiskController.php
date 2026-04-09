<?php

namespace App\Http\Controllers;

use App\Models\RiskRegister;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiskController extends Controller
{
    public function index(Request $request)
    {
        $query = RiskRegister::with('owner');

        if ($request->filled('category')) $query->where('category', $request->category);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('risk_code', 'like', "%{$request->search}%");
            });
        }

        $risks = $query->latest()->paginate(10);
        return view('risks.index', compact('risks'));
    }

    public function create()
    {
        $users = User::all();
        return view('risks.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:operational,financial,compliance,strategic,it',
            'inherent_likelihood' => 'required|integer|min:1|max:5',
            'inherent_impact' => 'required|integer|min:1|max:5',
            'residual_likelihood' => 'required|integer|min:1|max:5',
            'residual_impact' => 'required|integer|min:1|max:5',
            'owner_id' => 'required|exists:users,id',
        ]);

        $year = date('Y');
        $lastCode = RiskRegister::where('risk_code', 'like', "RISK-$year-%")->orderBy('risk_code', 'desc')->first();
        $nextNum = $lastCode ? intval(substr($lastCode->risk_code, -4)) + 1 : 1;
        $validated['risk_code'] = "RISK-$year-" . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
        $validated['status'] = 'active';

        $risk = RiskRegister::create($validated);
        AuditLog::log('create', 'risk', $risk->id, null, $risk->toArray());

        return redirect()->route('risks.show', $risk)->with('success', 'Risk registered successfully.');
    }

    public function show(RiskRegister $risk)
    {
        $risk->load(['owner', 'audits']);
        return view('risks.show', compact('risk'));
    }

    public function edit(RiskRegister $risk)
    {
        $users = User::all();
        return view('risks.edit', compact('risk', 'users'));
    }

    public function update(Request $request, RiskRegister $risk)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:operational,financial,compliance,strategic,it',
            'inherent_likelihood' => 'required|integer|min:1|max:5',
            'inherent_impact' => 'required|integer|min:1|max:5',
            'residual_likelihood' => 'required|integer|min:1|max:5',
            'residual_impact' => 'required|integer|min:1|max:5',
            'status' => 'required|in:active,mitigated,obsolete',
            'owner_id' => 'required|exists:users,id',
        ]);

        $oldData = $risk->toArray();
        $risk->update($validated);
        AuditLog::log('update', 'risk', $risk->id, $oldData, $risk->fresh()->toArray());

        return redirect()->route('risks.show', $risk)->with('success', 'Risk updated successfully.');
    }

    public function destroy(RiskRegister $risk)
    {
        AuditLog::log('delete', 'risk', $risk->id, $risk->toArray(), null);
        $risk->delete();
        return redirect()->route('risks.index')->with('success', 'Risk deleted.');
    }

    public function heatmap()
    {
        $risks = RiskRegister::where('status', 'active')->get();
        return view('risks.heatmap', compact('risks'));
    }
}
