@extends('layouts.app')
@section('title', 'Simulator Hardware (Fingerprint & RFID)')

@section('content')
<div class="max-w-md mx-auto py-8 px-4">
    <div class="glass-panel rounded-3xl shadow-xl overflow-hidden border border-white/20 bg-gradient-to-br from-indigo-500 to-purple-600 text-white">
        <div class="p-8 text-center">
            <div class="w-20 h-20 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4 backdrop-blur-md">
                <i class="fa-solid fa-microchip text-4xl"></i>
            </div>
            <h1 class="text-2xl font-bold">Hardware Simulator</h1>
            <p class="text-indigo-100 text-sm mt-2">Gunakan halaman ini untuk simulasi alat Fingerprint/RFID via HP</p>
        </div>

        <div class="bg-white rounded-t-3xl p-8 text-gray-800">
            <div class="space-y-6">
                {{-- Mode Selection --}}
                <div class="grid grid-cols-2 gap-4">
                    <button onclick="setMode('fingerprint')" id="btn-fp" class="p-4 rounded-2xl border-2 border-indigo-600 bg-indigo-50 text-indigo-700 font-bold transition-all flex flex-col items-center gap-2">
                        <i class="fa-solid fa-fingerprint text-2xl"></i>
                        Fingerprint
                    </button>
                    <button onclick="setMode('rfid')" id="btn-rfid" class="p-4 rounded-2xl border-2 border-gray-100 bg-gray-50 text-gray-400 font-bold transition-all flex flex-col items-center gap-2">
                        <i class="fa-solid fa-id-card text-2xl"></i>
                        RFID
                    </button>
                </div>

                <hr class="border-gray-100">

                {{-- Input Data --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">ID Alat / UID Kartu</label>
                    <input type="text" id="uid_input" value="SIMULASI-001" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-mono">
                </div>

                <div id="register-section" class="hidden">
                    <label class="block text-sm font-bold text-gray-700 mb-2">NIS Santri (Hanya untuk Register)</label>
                    <input type="text" id="nis_input" placeholder="Contoh: PST.2026.001" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-mono">
                </div>

                {{-- Actions --}}
                <div class="space-y-3 pt-4">
                    <button onclick="sendRequest('scan')" class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold shadow-lg shadow-indigo-200 transition-all active:scale-95">
                        TAP / SCAN (TRANSAKSI)
                    </button>
                    <button onclick="toggleRegister()" id="btn-reg-toggle" class="w-full py-3 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-2xl font-bold transition-all border border-emerald-100">
                        MODE REGISTRASI
                    </button>
                    <div id="btn-reg-action" class="hidden">
                        <button onclick="sendRequest('register')" class="w-full py-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-bold shadow-lg shadow-emerald-200 transition-all active:scale-95">
                            DAFTARKAN SEKARANG
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="log" class="mt-6 space-y-2"></div>
</div>

<script>
    let currentMode = 'fingerprint';
    let isRegisterMode = false;

    function setMode(mode) {
        currentMode = mode;
        document.getElementById('btn-fp').className = mode === 'fingerprint' ? 'p-4 rounded-2xl border-2 border-indigo-600 bg-indigo-50 text-indigo-700 font-bold transition-all flex flex-col items-center gap-2' : 'p-4 rounded-2xl border-2 border-gray-100 bg-gray-50 text-gray-400 font-bold transition-all flex flex-col items-center gap-2';
        document.getElementById('btn-rfid').className = mode === 'rfid' ? 'p-4 rounded-2xl border-2 border-indigo-600 bg-indigo-50 text-indigo-700 font-bold transition-all flex flex-col items-center gap-2' : 'p-4 rounded-2xl border-2 border-gray-100 bg-gray-50 text-gray-400 font-bold transition-all flex flex-col items-center gap-2';
        addLog(`Mode diganti ke: ${mode.toUpperCase()}`);
    }

    function toggleRegister() {
        isRegisterMode = !isRegisterMode;
        document.getElementById('register-section').classList.toggle('hidden');
        document.getElementById('btn-reg-action').classList.toggle('hidden');
        document.getElementById('btn-reg-toggle').innerText = isRegisterMode ? 'BATAL REGISTRASI' : 'MODE REGISTRASI';
        document.getElementById('btn-reg-toggle').className = isRegisterMode ? 'w-full py-3 bg-red-50 text-red-700 hover:bg-red-100 rounded-2xl font-bold transition-all border border-red-100' : 'w-full py-3 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-2xl font-bold transition-all border border-emerald-100';
    }

    async function sendRequest(action) {
        const uid = document.getElementById('uid_input').value;
        const nis = document.getElementById('nis_input').value;
        const url = `/api/${currentMode}/sync`;
        
        const payload = {
            device_id: 'MOBILE-SIMULATOR',
            action: action,
            timestamp: Math.floor(Date.now() / 1000)
        };

        if (currentMode === 'fingerprint') {
            payload.fingerprint_id = uid;
        } else {
            payload.rfid_uid = uid;
        }

        if (action === 'register') {
            payload.nis = nis;
        }

        addLog(`Mengirim ${action.toUpperCase()}...`, 'info');

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            });

            const result = await response.json();

            if (result.success) {
                addLog(`✅ Berhasil: ${result.message}`, 'success');
                if (result.data) {
                    addLog(`Santri: ${result.data.nama || result.data.nis}`, 'success');
                }
            } else {
                addLog(`❌ Gagal: ${result.message}`, 'error');
            }
        } catch (error) {
            addLog(`❌ Error Koneksi: ${error.message}`, 'error');
        }
    }

    function addLog(msg, type = 'default') {
        const log = document.getElementById('log');
        const entry = document.createElement('div');
        const colors = {
            success: 'bg-green-100 text-green-800 border-green-200',
            error: 'bg-red-100 text-red-800 border-red-200',
            info: 'bg-blue-100 text-blue-800 border-blue-200',
            default: 'bg-gray-100 text-gray-700 border-gray-200'
        };
        entry.className = `p-3 rounded-xl border text-xs font-medium ${colors[type] || colors.default} animate-pulse`;
        entry.innerText = `[${new Date().toLocaleTimeString()}] ${msg}`;
        log.prepend(entry);
    }
</script>
@endsection
