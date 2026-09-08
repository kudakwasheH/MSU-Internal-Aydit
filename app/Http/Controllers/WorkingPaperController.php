<?php

namespace App\Http\Controllers;

use App\Models\WorkingPaper;
use App\Models\Audit;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class WorkingPaperController extends Controller
{
    public function index()
    {
        $workingPapers = WorkingPaper::with(['audit', 'creator'])->latest()->paginate(10);
        return view('working_papers.index', compact('workingPapers'));
    }

    public function create(Request $request)
    {
        $selectedAudit = null;
        if ($request->has('audit_id')) {
            $selectedAudit = Audit::findOrFail($request->audit_id);
        }
        $audits = Audit::where('status', '!=', 'completed')->get();
        return view('working_papers.create', compact('audits', 'selectedAudit'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'audit_id' => 'required|exists:audits,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'required|string',
            'status' => 'required|in:draft,review,approved',
            'evidence_file' => 'nullable|file|max:10240', // 10MB limit
        ]);

        $validated['created_by'] = Auth::id();
        $validated['version'] = 1;

        if ($request->hasFile('evidence_file')) {
            $path = $request->file('evidence_file')->store('evidence', 'local');
            $validated['file_path'] = $path;
        }

        $wp = WorkingPaper::create($validated);
        
        AuditLog::log('create', 'working_paper', $wp->id, null, $wp->toArray());

        return redirect()->route('audits.show', $wp->audit_id)
            ->with('success', 'Working paper created successfully.');
    }

    public function show(WorkingPaper $workingPaper)
    {
        $workingPaper->load(['audit', 'creator']);
        return view('working_papers.show', compact('workingPaper'));
    }

    public function edit(WorkingPaper $workingPaper)
    {
        $audits = Audit::all();
        return view('working_papers.edit', compact('workingPaper', 'audits'));
    }

    public function update(Request $request, WorkingPaper $workingPaper)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'required|string',
            'status' => 'required|in:draft,review,approved',
            'evidence_file' => 'nullable|file|max:10240',
        ]);

        if ($request->hasFile('evidence_file')) {
            if ($workingPaper->file_path) {
                Storage::disk('local')->delete($workingPaper->file_path);
            }
            $path = $request->file('evidence_file')->store('evidence', 'local');
            $validated['file_path'] = $path;
        }

        $oldData = $workingPaper->toArray();
        $workingPaper->update($validated);
        $workingPaper->increment('version');

        AuditLog::log('update', 'working_paper', $workingPaper->id, $oldData, $workingPaper->fresh()->toArray());

        return redirect()->route('working-papers.show', $workingPaper)
            ->with('success', 'Working paper updated successfully.');
    }

    public function destroy(WorkingPaper $workingPaper)
    {
        if ($workingPaper->file_path) {
            Storage::disk('local')->delete($workingPaper->file_path);
        }
        
        AuditLog::log('delete', 'working_paper', $workingPaper->id, $workingPaper->toArray(), null);
        $workingPaper->delete();

        return redirect()->back()->with('success', 'Working paper deleted.');
    }

    public function download(WorkingPaper $workingPaper)
    {
        if (!$workingPaper->file_path || !Storage::disk('local')->exists($workingPaper->file_path)) {
            abort(404, 'Evidence file not found.');
        }

        return Storage::disk('local')->download($workingPaper->file_path);
    }
}
