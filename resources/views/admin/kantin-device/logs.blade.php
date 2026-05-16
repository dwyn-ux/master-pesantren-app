@extends('layouts.app')

@section('title', 'Log Sync Device')
@section('page-title', 'Log Sync: ' . $device->nama)

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $device->nama }}</h2>
                <p class="text-sm text-gray-500">{{ $device->device_code }} · {{ $device->outlet->nama ?? '-' }}</p>
            </div>
            <a href="{{ route('admin.kantin-device.index') }}" class="text-sm text-gray-600 hover:underline">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>

        @if($logs->isEmpty())
            <div class="text-center py-8 text-gray-400">
                <i class="fa-solid fa-inbox text-4xl mb-2"></i>
                <p class="text-sm">Belum ada log sync.</p>
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Waktu</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Arah</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Jenis</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Jumlah Record</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($logs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-xs text-gray-600">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($log->arah === 'pull')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fa-solid fa-arrow-down mr-1"></i> Pull
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                    <i class="fa-solid fa-arrow-up mr-1"></i> Push
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 capitalize">{{ $log->jenis }}</td>
                        <td class="px-4 py-3 text-center text-sm font-mono">{{ $log->jumlah_record }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($log->status === 'success')
                                <span class="text-green-600"><i class="fa-solid fa-check-circle"></i></span>
                            @elseif($log->status === 'partial')
                                <span class="text-orange-600"><i class="fa-solid fa-exclamation-circle"></i></span>
                            @else
                                <span class="text-red-600"><i class="fa-solid fa-times-circle"></i></span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">
                            @if($log->detail)
                                <code class="bg-gray-100 px-2 py-1 rounded text-[11px]">{{ json_encode($log->detail) }}</code>
                            @endif
                            @if($log->error_message)
                                <div class="text-red-600 mt-1">{{ $log->error_message }}</div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $logs->links() }}</div>
        @endif
    </div>
</div>
@endsection
