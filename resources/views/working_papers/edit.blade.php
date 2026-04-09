@extends('layouts.app')

@section('title', 'Edit Fieldwork: ' . $workingPaper->title)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('working-papers.show', $workingPaper) }}" class="text-[#004ea1] text-sm font-bold hover:underline mb-2 inline-block">
                <i class="fas fa-arrow-left mr-1"></i> Back to Review
            </a>
            <h2 class="text-2xl font-bold text-[#333]">Edit: {{ $workingPaper->title }}</h2>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Audit: {{ $workingPaper->audit->audit_code }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('working-papers.update', $workingPaper) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200">
             <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Status *</label>
                <select name="status" required class="w-full px-4 py-2 border border-slate-200 rounded-xl text-sm bg-slate-50 focus:ring-2 focus:ring-[#004ea1] transition-all font-bold text-[#004ea1]">
                    <option value="draft" {{ $workingPaper->status == 'draft' ? 'selected' : '' }}>Draft (Work in Progress)</option>
                    <option value="review" {{ $workingPaper->status == 'review' ? 'selected' : '' }}>Pending Review</option>
                    <option value="approved" {{ $workingPaper->status == 'approved' ? 'selected' : '' }}>Approved / Finalized</option>
                </select>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200 space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Working Paper Title *</label>
                <input type="text" name="title" required value="{{ old('title', $workingPaper->title) }}" 
                       class="w-full px-4 py-2 border border-slate-200 rounded-xl text-sm font-bold text-slate-700">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Brief Context</label>
                <input type="text" name="description" value="{{ old('description', $workingPaper->description) }}" 
                       class="w-full px-4 py-2 border border-slate-200 rounded-xl text-sm bg-slate-50">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Detailed Observations & Analysis *</label>
                <textarea name="content" rows="12" required 
                          class="w-full px-4 py-2 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-[#004ea1] transition-all leading-relaxed">{{ old('content', $workingPaper->content) }}</textarea>
            </div>
        </div>

        <div class="bg-slate-50 rounded-2xl p-6 border border-dashed border-slate-300">
            <label class="block text-xs font-bold text-[#004ea1] uppercase tracking-widest mb-4"><i class="fas fa-paperclip mr-1"></i> Update Evidence</label>
            @if($workingPaper->file_path)
                <div class="mb-4 flex items-center gap-3 p-3 bg-white border border-slate-200 rounded-xl shadow-sm">
                    <i class="fas fa-file-pdf text-red-500 text-lg"></i>
                    <div>
                        <p class="text-[10px] font-bold text-slate-700">Current Evidence Attached</p>
                        <p class="text-[9px] text-slate-400 font-mono">{{ basename($workingPaper->file_path) }}</p>
                    </div>
                </div>
            @endif
            
            <div class="flex items-center justify-center w-full">
                <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-24 border-2 border-slate-300 border-dashed rounded-xl cursor-pointer bg-white hover:bg-slate-50 transition-colors">
                    <div class="flex flex-col items-center justify-center py-4 text-center px-4">
                        <i class="fas fa-cloud-upload-alt text-xl text-[#004ea1] mb-1"></i>
                        <p class="text-[10px] text-slate-500 font-bold">Replace file (Optional)</p>
                    </div>
                    <input id="dropzone-file" type="file" name="evidence_file" class="hidden" />
                </label>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('working-papers.show', $workingPaper) }}" class="px-6 py-2 bg-slate-100 text-slate-500 text-sm font-bold rounded-xl">Cancel</a>
            <button type="submit" class="px-8 py-2 bg-[#004ea1] text-white text-sm font-bold rounded-xl shadow-md hover:bg-[#001c40] transition-all">
                <i class="fas fa-save mr-2"></i> Update Working Paper
            </button>
        </div>
    </form>
</div>
@endsection
