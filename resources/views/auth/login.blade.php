<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass-login {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(255,255,255,0.1) inset;
        }
        .bg-pattern {
            background-color: #1e1b4b;
            background-image: radial-gradient(circle at top right, #3730a3 0%, transparent 40%),
                              radial-gradient(circle at bottom left, #312e81 0%, transparent 40%);
        }
    </style>
</head>
<body class="bg-pattern min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
    
    <!-- Decorative background elements -->
    <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-indigo-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse"></div>
    <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-fuchsia-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse" style="animation-delay: 2s;"></div>

    <div class="w-full max-w-md relative z-10">
        <!-- Logo Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white/10 backdrop-blur-md rounded-2xl shadow-xl border border-white/20 mb-4 transform hover:scale-105 transition-transform duration-300">
                <img src="/logo-nobg.png" alt="Logo Ash-Shiddiq" class="w-12 h-12 drop-shadow-md">
            </div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight drop-shadow-sm">{{ config('app.name') }}</h1>
            <p class="text-indigo-200 mt-2 font-medium">Sistem Informasi Pesantren Terpadu</p>
        </div>

        <div class="glass-login rounded-3xl p-8 transform transition-all">
            <h2 class="text-xl font-bold text-gray-800 mb-6 text-center">Masuk ke Akun Anda</h2>
            
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-start gap-3 shadow-sm">
                    <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                    <span class="text-sm font-medium">{{ $errors->first() }}</span>
                </div>
            @endif

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-6 flex items-start gap-3 shadow-sm">
                    <i class="fa-solid fa-circle-check mt-0.5"></i>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('auth.login') }}" class="space-y-5">
                @csrf
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Username</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <input type="text" name="username" value="{{ old('username') }}"
                               class="w-full pl-11 pr-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all shadow-sm @error('username') border-red-500 ring-red-500/20 @enderror"
                               placeholder="Masukkan username" autofocus autocomplete="username">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Password</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <input type="password" name="password"
                               class="w-full pl-11 pr-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all shadow-sm @error('password') border-red-500 ring-red-500/20 @enderror"
                               placeholder="Masukkan password" autocomplete="current-password">
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center cursor-pointer group">
                        <div class="relative flex items-center justify-center w-5 h-5 mr-2">
                            <input type="checkbox" name="remember" id="remember" class="peer appearance-none w-5 h-5 border-2 border-gray-300 rounded focus:ring-2 focus:ring-indigo-500/30 focus:outline-none checked:bg-indigo-600 checked:border-indigo-600 transition-colors cursor-pointer">
                            <i class="fa-solid fa-check absolute text-white text-xs opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity"></i>
                        </div>
                        <span class="text-sm font-medium text-gray-600 group-hover:text-gray-800 transition-colors">Ingat saya</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition-all shadow-md shadow-indigo-200 hover:-translate-y-0.5 flex justify-center items-center gap-2 mt-2">
                    Masuk ke Sistem <i class="fa-solid fa-arrow-right-to-bracket"></i>
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-gray-100/80">
                <div class="bg-amber-50 border border-amber-100 rounded-xl p-4 flex items-start gap-3">
                    <div class="text-amber-500 mt-0.5"><i class="fa-solid fa-fingerprint"></i></div>
                    <div>
                        <h6 class="text-sm font-bold text-amber-800">Login Santri</h6>
                        <p class="text-xs text-amber-700 mt-1">Santri dapat login dengan menggunakan fingerprint reader pada perangkat komputer pesantren.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-6">
            <p class="text-indigo-200/60 text-xs font-medium">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
