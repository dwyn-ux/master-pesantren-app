@extends('layouts.app')
@section('title', 'Notifikasi')
@section('page-title', 'Notifikasi')

@section('content')
<div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <h4 class="font-bold text-gray-700">Inbox Notifikasi</h4>
        <form method="POST" action="{{ route('notifications.mark-all') }}">
            @csrf
            <button class="text-xs text-indigo-600 hover:underline">Tandai semua sudah dibaca</button>
        </form>
    </div>

    <div class="divide-y divide-gray-100">
        @forelse($notifications as $n)
            @php
                $iconMap = [
                    'kesehatan'        => ['bg' => 'bg-red-100 text-red-600',     'icon' => 'fa-heart-pulse'],
                    'weekly_report'    => ['bg' => 'bg-teal-100 text-teal-600',   'icon' => 'fa-chart-line'],
                    'monthly_report'   => ['bg' => 'bg-indigo-100 text-indigo-600','icon' => 'fa-calendar-days'],
                    'voice_note_ustadz'=> ['bg' => 'bg-emerald-100 text-emerald-600','icon' => 'fa-microphone'],
                    'voice_note_wali'  => ['bg' => 'bg-purple-100 text-purple-600','icon' => 'fa-microphone-lines'],
                    'tagihan_baru'     => ['bg' => 'bg-amber-100 text-amber-600', 'icon' => 'fa-file-invoice-dollar'],
                    'quran_reminder'   => ['bg' => 'bg-green-100 text-green-600', 'icon' => 'fa-book-quran'],
                    'finance_reminder' => ['bg' => 'bg-orange-100 text-orange-600','icon' => 'fa-triangle-exclamation'],
                    'finance_approval' => ['bg' => 'bg-orange-100 text-orange-600','icon' => 'fa-triangle-exclamation'],
                    'budget_alert'     => ['bg' => 'bg-orange-100 text-orange-600','icon' => 'fa-triangle-exclamation'],
                ];
                $style = $iconMap[$n->type] ?? ['bg' => 'bg-gray-100 text-gray-500', 'icon' => 'fa-bell'];
            @endphp
            <a href="{{ $n->action_url ?? route('notifications.read', $n) }}"
               class="flex items-start gap-3 px-5 py-4 hover:bg-gray-50/60 transition {{ $n->read_at ? '' : 'bg-indigo-50/30' }}">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg {{ $style['bg'] }}">
                    <i class="fa-solid {{ $style['icon'] }}"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <p class="font-semibold text-sm text-gray-800 {{ $n->read_at ? '' : 'text-indigo-700' }}">{{ $n->title }}</p>
                        <span class="text-xs text-gray-400 whitespace-nowrap ml-2">{{ $n->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">{{ $n->body }}</p>
                </div>
            </a>
        @empty
            <div class="px-5 py-12 text-center text-gray-400 text-sm">Belum ada notifikasi.</div>
        @endforelse
    </div>
</div>

<div class="mt-4">{{ $notifications->links() }}</div>
@endsection
