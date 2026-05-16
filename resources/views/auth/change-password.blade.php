<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ganti Password — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .bg-pattern {
            background-color: #1e1b4b;
            background-image: radial-gradient(circle at top right, #3730a3 0%, transparent 40%),
                              radial-gradient(circle at bottom left, #312e81 0%, transparent 40%);
        }
    </style>
</head>
<body class="bg-pattern min-h-screen flex items-center justify-center p-4 relative overflow-hidden">

    <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-amber-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"></div>
    <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-indigo-500 rounded-full mix-blend-multiply filter blur-3xl opacity-25 animate-pulse" style="animation-delay: 2s;"></div>

    <div class="w-full max-w-md relative z-10">
        <!-- Icon Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-amber-400/20 backdrop-blur-md rounded-2xl shadow-xl border border-amber-400/30 mb-4">
                <i class="fa-solid fa-shield-halved text-4xl text-amber-300 drop-shadow-md"></i>
            </div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Ganti Password</h1>
            <p class="text-indigo-200 mt-1 font-medium text-sm">Buat password baru untuk keamanan akun Anda</p>
        </div>

        <div class="bg-white/95 backdrop-blur-xl rounded-3xl p-8 shadow-2xl border border-white/50">
            <div class="bg-amber-50 border border-amber-100 rounded-2xl p-4 mb-6 flex items-start gap-3">
                <div class="text-amber-500 mt-0.5 text-lg shrink-0"><i class="fa-solid fa-circle-exclamation"></i></div>
                <div>
                    <p class="text-sm font-bold text-amber-800">Halo, <span class="text-amber-600">{{ auth()->user()->name }}</span>!</p>
                    <p class="text-xs text-amber-700 mt-1 leading-relaxed">Silakan buat password baru sebelum melanjutkan menggunakan sistem.</p>
                </div>
            </div>

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-start gap-3 text-sm font-medium">
                    <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('auth.change-password.update') }}" class="space-y-5">
                @csrf @method('POST')

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Password Baru</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <input type="password" name="password"
                               class="w-full pl-11 pr-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all shadow-sm @error('password') border-red-500 @enderror"
                               placeholder="Minimal 8 karakter">
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Konfirmasi Password</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                            <i class="fa-solid fa-lock-open"></i>
                        </div>
                        <input type="password" name="password_confirmation"
                               class="w-full pl-11 pr-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all shadow-sm"
                               placeholder="Ulangi password baru">
                    </div>
                </div>

                <button type="submit" class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition-all shadow-md shadow-indigo-200 hover:-translate-y-0.5 flex justify-center items-center gap-2 mt-2">
                    <i class="fa-solid fa-check-circle"></i> Simpan Password Baru
                </button>
            </form>

            <div class="mt-6 pt-5 border-t border-gray-100 text-center">
                <form method="POST" action="{{ route('auth.logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-gray-400 hover:text-gray-600 transition-colors font-medium">
                        <i class="fa-solid fa-right-from-bracket mr-1"></i> Keluar dari sistem
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
