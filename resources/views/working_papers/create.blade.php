@extends('layouts.app')

@section('title', 'Document Fieldwork')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ isset($selectedAudit) ? route('audits.show', $selectedAudit) : route('dashboard') }}" class="text-[#004ea1] text-sm font-bold hover:underline mb-2 inline-block">
            <i class="fas fa-arrow-left mr-1"></i> Back to Audit
        </a>
        <h2 class="text-2xl font-bold text-[#333]">New Working Paper</h2>
        <p class="text-sm text-gray-500">Document control testing, findings, and evidence for the audit engagement.</p>
    </div>

    <form method="POST" action="{{ route('working-papers.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        {{-- Audit Selection --}}
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Audit Engagement *</label>
                    <select name="audit_id" required class="w-full px-4 py-2 border border-slate-200 rounded-xl text-sm bg-slate-50 focus:ring-2 focus:ring-[#004ea1] transition-all">
                        <option value="">Select Audit</option>
                        @foreach($audits as $audit)
                            <option value="{{ $audit->id }}" {{ (isset($selectedAudit) && $selectedAudit->id == $audit->id) ? 'selected' : '' }}>
                                {{ $audit->audit_code }}: {{ $audit->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Status *</label>
                    <select name="status" required class="w-full px-4 py-2 border border-slate-200 rounded-xl text-sm bg-slate-50 focus:ring-2 focus:ring-[#004ea1] transition-all">
                        <option value="draft">Draft (Work in Progress)</option>
                        <option value="review">Pending Review</option>
                        <option value="approved">Approved / Finalized</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Content Section --}}
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200 space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Working Paper Title *</label>
                <input type="text" name="title" placeholder="e.g., Control Test: User Access Review" required value="{{ old('title') }}" 
                       class="w-full px-4 py-2 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-[#004ea1] transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Brief Context</label>
                <input type="text" name="description" placeholder="Short description of the test objective" value="{{ old('description') }}" 
                       class="w-full px-4 py-2 border border-slate-200 rounded-xl text-sm bg-slate-50">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Detailed Observations & Analysis *</label>
                <textarea name="content" rows="10" required placeholder="Describe your methodology, samples tested, and specific observations..." 
                          class="w-full px-4 py-2 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-[#004ea1] transition-all">{{ old('content') }}</textarea>
                <p class="mt-2 text-[10px] text-slate-400 italic"><i class="fas fa-info-circle mr-1"></i> Tip: Use templates for IT/System reviews or Procurement analysis for consistency.</p>
            </div>
        </div>

        {{-- Evidence Upload --}}
        <div class="bg-slate-50 rounded-2xl p-6 border border-dashed border-slate-300">
            <label class="block text-xs font-bold text-[#004ea1] uppercase tracking-widest mb-4"><i class="fas fa-paperclip mr-1"></i> Evidence Repository (Upload Evidence)</label>
            
            <div class="flex items-center justify-center w-full">
                <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-32 border-2 border-slate-300 border-dashed rounded-xl cursor-not-allowed bg-white hover:bg-slate-50 transition-colors">
                    <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center px-4">
                        <i class="fas fa-cloud-upload-alt text-2xl text-[#004ea1] mb-3"></i>
                        <p class="mb-1 text-xs text-slate-500 font-bold">Select evidence file (PDF, Excel, JPG)</p>
                        <p class="text-[10px] text-slate-400 italic">Simulated Upload for MSU Internal Audit Environment (MAX 10MB)</p>
                    </div>
                    <input id="dropzone-file" type="file" name="evidence_file" class="hidden" />
                </label>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ url()->previous() }}" class="px-6 py-2 bg-slate-100 text-slate-500 text-sm font-bold rounded-xl hover:bg-slate-200 transition-all">Discard</a>
            <button type="submit" class="px-8 py-2 bg-[#004ea1] text-white text-sm font-bold rounded-xl shadow-md hover:bg-[#001c40] transition-all">
                <i class="fas fa-save mr-2"></i> Save Working Paper
            </button>
        </div>
    </form>
</div>
@endsection
