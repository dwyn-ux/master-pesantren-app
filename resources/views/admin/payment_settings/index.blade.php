@extends('layouts.app')

@section('title', 'Pengaturan Pembayaran')
@section('page-title', 'Multi-Payment Gateway')

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('content')
<div class="max-w-5xl mx-auto" x-data="{ activeTab: '{{ old('active_gateway', $setting->active_gateway ?? 'tripay') }}' }">
    <div class="glass-panel rounded-2xl overflow-hidden shadow-sm">
        
        <div class="px-6 py-5 border-b border-gray-200/50 bg-white/50">
            <h4 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-credit-card text-indigo-600"></i> Pengaturan Payment Gateway
            </h4>
            <p class="text-sm text-gray-500 mt-1">Konfigurasi API Keys untuk Tripay, Midtrans, dan Xendit.</p>
        </div>

        <form action="{{ route('admin.payment-settings.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="p-6">
                <!-- Gateway Selector -->
                <div class="mb-8">
                    <label class="block text-sm font-semibold text-gray-700 mb-3 uppercase tracking-wider">Gateway Aktif Saat Ini</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Tripay -->
                        <label class="relative cursor-pointer">
                            <input type="radio" name="active_gateway" value="tripay" x-model="activeTab" class="peer sr-only">
                            <div class="p-5 border-2 rounded-xl transition-all"
                                 :class="activeTab === 'tripay' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 bg-white hover:border-indigo-300'">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xl">T</div>
                                        <div>
                                            <h5 class="font-bold text-gray-800">Tripay</h5>
                                            <p class="text-xs text-gray-500">Virtual Account & QRIS</p>
                                        </div>
                                    </div>
                                    <div x-show="activeTab === 'tripay'" class="w-6 h-6 rounded-full bg-indigo-500 text-white flex items-center justify-center text-sm shadow-md">
                                        <i class="fa-solid fa-check"></i>
                                    </div>
                                </div>
                            </div>
                        </label>
                        
                        <!-- Midtrans -->
                        <label class="relative cursor-pointer">
                            <input type="radio" name="active_gateway" value="midtrans" x-model="activeTab" class="peer sr-only">
                            <div class="p-5 border-2 rounded-xl transition-all"
                                 :class="activeTab === 'midtrans' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 bg-white hover:border-indigo-300'">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-teal-100 flex items-center justify-center text-teal-600 font-bold text-xl">M</div>
                                        <div>
                                            <h5 class="font-bold text-gray-800">Midtrans</h5>
                                            <p class="text-xs text-gray-500">GoPay, ShopeePay, VA</p>
                                        </div>
                                    </div>
                                    <div x-show="activeTab === 'midtrans'" class="w-6 h-6 rounded-full bg-indigo-500 text-white flex items-center justify-center text-sm shadow-md">
                                        <i class="fa-solid fa-check"></i>
                                    </div>
                                </div>
                            </div>
                        </label>

                        <!-- Xendit -->
                        <label class="relative cursor-pointer">
                            <input type="radio" name="active_gateway" value="xendit" x-model="activeTab" class="peer sr-only">
                            <div class="p-5 border-2 rounded-xl transition-all"
                                 :class="activeTab === 'xendit' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 bg-white hover:border-indigo-300'">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-sky-100 flex items-center justify-center text-sky-600 font-bold text-xl">X</div>
                                        <div>
                                            <h5 class="font-bold text-gray-800">Xendit</h5>
                                            <p class="text-xs text-gray-500">Retail Outlet & E-Wallet</p>
                                        </div>
                                    </div>
                                    <div x-show="activeTab === 'xendit'" class="w-6 h-6 rounded-full bg-indigo-500 text-white flex items-center justify-center text-sm shadow-md">
                                        <i class="fa-solid fa-check"></i>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="border-t border-gray-200/60 my-6"></div>

                <!-- API Keys Configuration -->
                <div class="bg-gray-50/50 rounded-xl border border-gray-100 p-6">
                    
                    <!-- Tripay Settings -->
                    <div x-show="activeTab === 'tripay'" x-transition>
                        <div class="flex items-center justify-between mb-4 border-b pb-2">
                            <h5 class="font-bold text-gray-800"><i class="fa-solid fa-key text-blue-500 mr-2"></i>Kredensial Tripay</h5>
                            <button type="button" id="btn-test-tripay" onclick="testTripayConnection()"
                                class="inline-flex items-center gap-2 px-4 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 rounded-lg text-sm font-medium transition-colors">
                                <i class="fa-solid fa-plug-circle-check" id="icon-test-tripay"></i>
                                <span id="label-test-tripay">Tes Koneksi API</span>
                            </button>
                        </div>
                        <div id="test-tripay-result" class="hidden mb-4 p-3 rounded-lg text-sm font-medium"></div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">API Key</label>
                                <input type="text" name="tripay_api_key" value="{{ old('tripay_api_key', $setting->tripay_api_key ?? '') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Private Key</label>
                                <input type="password" name="tripay_private_key" value="{{ old('tripay_private_key', $setting->tripay_private_key ?? '') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Merchant Code</label>
                                <input type="text" name="tripay_merchant_code" value="{{ old('tripay_merchant_code', $setting->tripay_merchant_code ?? '') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Environment Mode</label>
                                <select name="tripay_mode" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="sandbox" {{ (old('tripay_mode', $setting->tripay_mode ?? '') == 'sandbox') ? 'selected' : '' }}>Sandbox (Testing)</option>
                                    <option value="production" {{ (old('tripay_mode', $setting->tripay_mode ?? '') == 'production') ? 'selected' : '' }}>Production (Live)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Midtrans Settings -->
                    <div x-show="activeTab === 'midtrans'" x-transition style="display: none;">
                        <h5 class="font-bold text-gray-800 mb-4 border-b pb-2"><i class="fa-solid fa-key text-teal-500 mr-2"></i>Kredensial Midtrans</h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Client Key</label>
                                <input type="text" name="midtrans_client_key" value="{{ old('midtrans_client_key', $setting->midtrans_client_key ?? '') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Server Key</label>
                                <input type="password" name="midtrans_server_key" value="{{ old('midtrans_server_key', $setting->midtrans_server_key ?? '') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div class="col-span-2">
                                <label class="flex items-center">
                                    <input type="checkbox" name="midtrans_is_production" value="1" {{ old('midtrans_is_production', $setting->midtrans_is_production ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">Gunakan Environment Production (Live)</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Xendit Settings -->
                    <div x-show="activeTab === 'xendit'" x-transition style="display: none;">
                        <h5 class="font-bold text-gray-800 mb-4 border-b pb-2"><i class="fa-solid fa-key text-sky-500 mr-2"></i>Kredensial Xendit</h5>
                        <div class="grid grid-cols-1 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Secret API Key</label>
                                <input type="password" name="xendit_secret_key" value="{{ old('xendit_secret_key', $setting->xendit_secret_key ?? '') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="xnd_development_...">
                                <p class="mt-1 text-xs text-gray-500">Masukkan API Key baik untuk development maupun production.</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50/80 border-t border-gray-200/50 flex justify-end">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-6 rounded-xl shadow-sm transition-colors flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function testTripayConnection() {
    const btn   = document.getElementById('btn-test-tripay');
    const icon  = document.getElementById('icon-test-tripay');
    const label = document.getElementById('label-test-tripay');
    const result = document.getElementById('test-tripay-result');

    btn.disabled = true;
    icon.className = 'fa-solid fa-spinner fa-spin';
    label.textContent = 'Menguji...';
    result.className = 'hidden mb-4 p-3 rounded-lg text-sm font-medium';

    fetch('{{ route('admin.payment-settings.test') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
    })
    .then(r => r.json())
    .then(data => {
        result.textContent = data.message;
        result.className = 'mb-4 p-3 rounded-lg text-sm font-medium ' +
            (data.success ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-red-50 text-red-800 border border-red-200');
    })
    .catch(() => {
        result.textContent = 'Terjadi kesalahan jaringan.';
        result.className = 'mb-4 p-3 rounded-lg text-sm font-medium bg-red-50 text-red-800 border border-red-200';
    })
    .finally(() => {
        btn.disabled = false;
        icon.className = 'fa-solid fa-plug-circle-check';
        label.textContent = 'Tes Koneksi API';
    });
}
</script>
@endsection
