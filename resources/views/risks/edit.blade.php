@extends('layouts.app')
@section('title', 'Edit Risk')
@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-lg font-bold text-[#333] mb-6">Edit: {{ $risk->risk_code }}</h2>
        <form method="POST" action="{{ route('risks.update', $risk) }}" class="space-y-5">
            @csrf @method('PUT')
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Title *</label><input type="text" name="title" value="{{ old('title', $risk->title) }}" required class="w-full px-3 py-2 border rounded-lg text-sm"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Description *</label><textarea name="description" rows="3" required class="w-full px-3 py-2 border rounded-lg text-sm">{{ old('description', $risk->description) }}</textarea></div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Category *</label><select name="category" required class="w-full px-3 py-2 border rounded-lg text-sm">@foreach(['operational','financial','compliance','strategic','it'] as $c)<option value="{{ $c }}" {{ old('category', $risk->category) == $c ? 'selected' : '' }}>{{ ucfirst($c) }}</option>@endforeach</select></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Status *</label><select name="status" required class="w-full px-3 py-2 border rounded-lg text-sm">@foreach(['active','mitigated','obsolete'] as $s)<option value="{{ $s }}" {{ old('status', $risk->status) == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>@endforeach</select></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Owner *</label><select name="owner_id" required class="w-full px-3 py-2 border rounded-lg text-sm">@foreach($users as $u)<option value="{{ $u->id }}" {{ old('owner_id', $risk->owner_id) == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>@endforeach</select></div>
            </div>
            <div class="space-y-4">
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 space-y-4">
                    <h3 class="text-sm font-bold text-slate-700 flex items-center gap-2"><i class="fas fa-bolt text-red-500"></i> Inherent Risk Assessment</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Likelihood (1-5)</label>
                            <select name="inherent_likelihood" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white">
                                @for($i=1;$i<=5;$i++) 
                                    <option value="{{ $i }}" {{ old('inherent_likelihood', $risk->inherent_likelihood) == $i ? 'selected' : '' }}>
                                        {{ $i }} - @if($i==1) Rare @elseif($i==2) Unlikely @elseif($i==3) Possible @elseif($i==4) Likely @else Almost Certain @endif
                                    </option> 
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Impact (1-5)</label>
                            <select name="inherent_impact" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white">
                                @for($i=1;$i<=5;$i++) 
                                    <option value="{{ $i }}" {{ old('inherent_impact', $risk->inherent_impact) == $i ? 'selected' : '' }}>
                                        {{ $i }} - @if($i==5) Catastrophic @elseif($i==4) Major @elseif($i==3) Moderate @elseif($i==2) Minor @else Insignificant @endif
                                    </option> 
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-[#004ea1]/5 rounded-xl border border-[#004ea1]/10 space-y-4">
                    <h3 class="text-sm font-bold text-[#004ea1] flex items-center gap-2"><i class="fas fa-shield-alt"></i> Residual Risk Assessment</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Likelihood (1-5)</label>
                            <select name="residual_likelihood" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white">
                                @for($i=1;$i<=5;$i++) 
                                    <option value="{{ $i }}" {{ old('residual_likelihood', $risk->residual_likelihood) == $i ? 'selected' : '' }}>
                                        {{ $i }} - @if($i==1) Rare @elseif($i==2) Unlikely @elseif($i==3) Possible @elseif($i==4) Likely @else Almost Certain @endif
                                    </option> 
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Impact (1-5)</label>
                            <select name="residual_impact" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white">
                                @for($i=1;$i<=5;$i++) 
                                    <option value="{{ $i }}" {{ old('residual_impact', $risk->residual_impact) == $i ? 'selected' : '' }}>
                                        {{ $i }} - @if($i==5) Catastrophic @elseif($i==4) Major @elseif($i==3) Moderate @elseif($i==2) Minor @else Insignificant @endif
                                    </option> 
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex gap-3 pt-4 border-t">
                <button type="submit" class="px-6 py-2 bg-[#004ea1] text-white text-sm font-medium rounded-lg"><i class="fas fa-save mr-2"></i>Update</button>
                <a href="{{ route('risks.show', $risk) }}" class="px-6 py-2 bg-gray-200 text-gray-700 text-sm rounded-lg">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

