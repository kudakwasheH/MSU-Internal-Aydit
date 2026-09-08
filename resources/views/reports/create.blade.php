@extends('layouts.app')
@section('title', 'Create Draft Report')
@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-lg font-bold text-[#333] mb-6"><i class="fas fa-plus-circle text-[#004ea1] mr-2"></i>Create Draft Report for {{ $audit->audit_code }}</h2>
        <form method="POST" action="{{ route('reports.store') }}" class="space-y-5">
            @csrf
            <input type="hidden" name="audit_id" value="{{ $audit->id }}">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Initial Comments</label>
                <textarea name="comments" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#004ea1] focus:border-[#004ea1]"></textarea>
            </div>
            <div class="flex items-center gap-3 pt-4 border-t">
                <button type="submit" class="px-6 py-2 bg-[#004ea1] text-white text-sm font-medium rounded-lg hover:bg-[#001533] transition">
                    <i class="fas fa-save mr-2"></i>Create Draft Report
                </button>
                <a href="{{ route('audits.show', $audit) }}" class="px-6 py-2 bg-gray-200 text-gray-700 text-sm rounded-lg hover:bg-gray-300 transition">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
