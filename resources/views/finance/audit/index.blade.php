@extends('layouts.app')
@section('title', 'Audit Trail')
@section('page-title', 'Audit Trail')

@section('sidebar')
    @include('partials.sidebar-finance')
@endsection

@section('content')
<form method="GET" class="glass-panel p-4 rounded-2xl mb-4 grid grid-cols-1 md:grid-cols-5 gap-3">
    <input type="text" name="action" value="{{ request('action') }}" placeholder="Action..." class="rounded-lg border-gray-300 text-sm">
    <input type="date" name="dari" value="{{ request('dari') }}" class="rounded-lg border-gray-300 text-sm">
    <input type="date" name="sampai" value="{{ request('sampai') }}" class="rounded-lg border-gray-300 text-sm">
    <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm">Filter</button>
    <a href="{{ route('finance.audit.index') }}" class="px-4 py-2 bg-gray-200 rounded-lg text-sm text-center">Reset</a>
</form>

<div class="glass-panel rounded-2xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase">
            <tr>
                <th class="px-3 py-2 text-left">Waktu</th>
                <th class="px-3 py-2 text-left">User</th>
                <th class="px-3 py-2 text-left">Aksi</th>
                <th class="px-3 py-2 text-left">Subjek</th>
                <th class="px-3 py-2 text-left">IP</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($items as $log)
            <tr class="hover:bg-indigo-50/30">
                <td class="px-3 py-2 text-xs whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                <td class="px-3 py-2 font-semibold">{{ $log->user?->name ?? '— sistem —' }}</td>
                <td class="px-3 py-2"><span class="px-2 py-1 rounded-full bg-blue-100 text-blue-700 text-xs">{{ $log->action }}</span></td>
                <td class="px-3 py-2 text-xs">
                    @if($log->subject_type)
                        <span class="font-mono text-gray-500">{{ class_basename($log->subject_type) }}#{{ $log->subject_id }}</span>
                    @endif
                </td>
                <td class="px-3 py-2 text-xs text-gray-500 font-mono">{{ $log->ip ?: '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-3 py-8 text-center text-gray-400">Belum ada log audit.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4">{{ $items->links() }}</div>
</div>
@endsection
