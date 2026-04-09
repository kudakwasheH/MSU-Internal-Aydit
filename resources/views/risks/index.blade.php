@extends('layouts.app')
@section('title', 'Risk Register')
@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-bold text-[#333]">Risk Register</h2>
        <p class="text-sm text-gray-500">Manage and monitor organizational risks</p>
    </div>
    <a href="{{ route('risks.create') }}" class="px-4 py-2 bg-[#004ea1] text-white text-sm font-medium rounded-lg hover:bg-[#001533] transition"><i class="fas fa-plus mr-2"></i>Register Risk</a>
</div>
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[180px]">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search risks..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#004ea1]">
        </div>
        <select name="category" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
            <option value="">All Categories</option>
            @foreach(['operational','financial','compliance','strategic','it'] as $c)
            <option value="{{ $c }}" {{ request('category') == $c ? 'selected' : '' }}>{{ ucfirst($c) }}</option>
            @endforeach
        </select>
        <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
            <option value="">All Status</option>
            @foreach(['active','mitigated','obsolete'] as $s)
            <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 bg-[#333] text-white text-sm rounded-lg"><i class="fas fa-filter mr-1"></i>Filter</button>
    </form>
</div>
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase">Inherent</th>
                <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 uppercase">Residual</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Level</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Owner</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($risks as $risk)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3 font-mono text-xs text-[#004ea1] font-semibold">{{ $risk->risk_code }}</td>
                <td class="px-5 py-3"><a href="{{ route('risks.show', $risk) }}" class="font-medium text-gray-800 hover:text-[#004ea1]">{{ Str::limit($risk->title, 30) }}</a></td>
                <td class="px-5 py-3"><span class="px-2 py-1 text-xs bg-gray-100 rounded-full">{{ ucfirst($risk->category) }}</span></td>
                <td class="px-5 py-3 text-center font-bold">{{ $risk->inherent_risk_score }}</td>
                <td class="px-5 py-3 text-center font-bold">{{ $risk->residual_risk_score }}</td>
                <td class="px-5 py-3"><span class="px-2 py-1 text-xs rounded-full font-medium" style="background-color: {{ $risk->risk_color }}20; color: {{ $risk->risk_color }}">{{ $risk->risk_level }}</span></td>
                <td class="px-5 py-3 text-xs text-gray-500">{{ $risk->owner?->name }}</td>
                <td class="px-5 py-3">
                    <a href="{{ route('risks.show', $risk) }}" class="text-blue-600 hover:text-blue-800 mr-2"><i class="fas fa-eye"></i></a>
                    <a href="{{ route('risks.edit', $risk) }}" class="text-[#ffcc00] hover:text-yellow-600"><i class="fas fa-edit"></i></a>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="px-5 py-8 text-center text-gray-400">No risks found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-5 py-3 border-t">{{ $risks->withQueryString()->links() }}</div>
</div>
@endsection

