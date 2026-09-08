@extends('layouts.app')

@section('title', 'Working Paper: ' . $workingPaper->title)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('audits.show', $workingPaper->audit_id) }}" class="text-[#004ea1] text-sm font-bold hover:underline mb-2 inline-block">
                <i class="fas fa-arrow-left mr-1"></i> Back to Audit
            </a>
            <h2 class="text-2xl font-bold text-[#333]">{{ $workingPaper->title }}</h2>
            <div class="flex items-center gap-3 mt-1">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">v{{ $workingPaper->version }} &middot; Created By {{ $workingPaper->creator->name }}</span>
                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider {{ $workingPaper->status == 'approved' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                    {{ ucfirst($workingPaper->status) }}
                </span>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('working-papers.edit', $workingPaper) }}" class="px-4 py-2 bg-[#ffcc00] text-[#333] text-sm font-bold rounded-xl hover:shadow-md transition-all">
                <i class="fas fa-edit mr-1"></i> Edit
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        {{-- Metadata sidebar --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                <h3 class="text-xs font-bold text-slate-400 mb-4 uppercase tracking-[0.2em]">Context</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Audit Link</p>
                        <p class="text-xs font-bold text-[#004ea1]">{{ $workingPaper->audit->audit_code }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Created Date</p>
                        <p class="text-xs font-bold text-slate-700">{{ $workingPaper->created_at->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Evidence Attached</p>
                        @if($workingPaper->file_path)
                            <a href="{{ route('working-papers.download', $workingPaper) }}" target="_blank" class="text-xs font-bold text-[#004ea1] hover:underline flex items-center gap-1 mt-1">
                                <i class="fas fa-file-pdf"></i> View Attachment
                            </a>
                        @else
                            <p class="text-xs text-slate-400 italic">No evidence uploaded.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Content body --}}
        <div class="lg:col-span-3 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 min-h-[600px] relative">
                @if($workingPaper->status == 'approved')
                    <div class="absolute top-8 right-8 text-[#28a745]/10 rotate-12">
                        <i class="fas fa-stamp text-[120px]"></i>
                    </div>
                @endif
                
                <div class="mb-8 pb-6 border-b border-slate-100">
                    <p class="text-xs font-bold text-slate-400 mb-2 uppercase tracking-widest">Description & Methodology</p>
                    <p class="text-slate-600 italic text-sm">{{ $workingPaper->description ?? 'No context provided.' }}</p>
                </div>

                <div class="prose max-w-none text-slate-800 leading-relaxed whitespace-pre-wrap">{{ $workingPaper->content }}</div>

                <div class="mt-12 pt-8 border-t border-slate-100">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center text-slate-400">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Auditor Signature</p>
                            <p class="text-sm font-bold text-[#004ea1]">{{ $workingPaper->creator->name }}</p>
                            <p class="text-[10px] text-slate-400 italic">{{ now()->toFormattedDateString() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
