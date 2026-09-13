<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FITONE</title>
    <script src="https://cdn.tailwindcss.com"></script>
        <link rel="shortcut ikon" href="{{ asset('storage/icon.png') }}" type="image/x-icon">
    <link rel="apple-touch-ikon" href="{{ asset('storage/icon.png') }}" sizes="180x180">
    <!-- Tambahan Alpine.js untuk interaksi Show/Hide Password -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> 
        body { font-family: 'Poppins', sans-serif; } 
        /* Mencegah icon berkedip saat halaman di-load */
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gradient-to-br from-[#0B1120] to-[#1a365d] min-h-screen flex items-center justify-center p-6 relative overflow-hidden">
    
    <!-- Ornamen Background -->
    <div class="absolute top-1/4 -left-20 w-72 h-72 bg-[#1ba1e2] rounded-full mix-blend-screen filter blur-[80px] opacity-20"></div>
    <div class="absolute bottom-1/4 -right-20 w-72 h-72 bg-white rounded-full mix-blend-screen filter blur-[80px] opacity-10"></div>

    <!-- Card Login -->
    <div class="bg-white/95 backdrop-blur-xl rounded-[2rem] shadow-2xl w-full max-w-md p-10 relative z-10 border border-white/20">
        
        <!-- Logo Cantik -->
        <div class="flex justify-center mb-6">
            <div class="p-4 bg-white rounded-full shadow-[0_0_25px_rgba(27,161,226,0.3)] border border-gray-100 transform hover:scale-105 transition-transform duration-300">
                <img src="{{ asset('storage/icon.png') }}" alt="Logo FITC" class="h-20 w-20 object-contain drop-shadow-md">
            </div>
        </div>

        <div class="text-center mb-10">
            <h2 class="text-3xl font-extrabold text-[#1a365d] tracking-tight">Login</h2>
            <p class="text-gray-500 text-sm mt-2 font-medium">Masuk untuk mengelola sistem FITONE</p>
        </div>

        <!-- Menampilkan Error jika gagal login -->
        @if($errors->any())
            <div class="bg-red-50 text-red-500 p-4 rounded-xl mb-6 border border-red-100 text-sm text-center font-medium animate-pulse">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ url('/login') }}" method="POST">
            @csrf
            
            <!-- Input Email -->
            <div class="mb-5">
                <label class="block text-[#1a365d] text-sm font-bold mb-2">Email Address</label>
                <div class="relative">
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full px-5 py-4 rounded-xl border border-gray-200 focus:outline-none focus:border-[#1ba1e2] focus:ring-2 focus:ring-[#1ba1e2]/20 transition-all font-medium text-gray-700 bg-gray-50 focus:bg-white" placeholder="user@fitc.com" required>
                </div>
            </div>
            
            <!-- Input Password dengan Alpine.js -->
            <div class="mb-8" x-data="{ showPassword: false }">
                <label class="block text-[#1a365d] text-sm font-bold mb-2">Password</label>
                <div class="relative">
                    <!-- Tipe input berubah dinamis antara 'text' dan 'password' -->
                    <input :type="showPassword ? 'text' : 'password'" name="password" class="w-full px-5 py-4 pr-14 rounded-xl border border-gray-200 focus:outline-none focus:border-[#1ba1e2] focus:ring-2 focus:ring-[#1ba1e2]/20 transition-all font-medium text-gray-700 bg-gray-50 focus:bg-white" placeholder="••••••••" required>
                    
                    <!-- Tombol Mata (Toggle) -->
                    <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-[#1ba1e2] focus:outline-none transition-colors">
                        
                        <!-- Icon Eye (Mata Terbuka - Tampil saat password disembunyikan) -->
                        <svg x-show="!showPassword" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>

                        <!-- Icon Eye-Slash (Mata Dicoret - Tampil saat password dilihat) -->
                        <svg x-show="showPassword" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <button type="submit" class="w-full bg-gradient-to-r from-[#1a365d] to-[#1ba1e2] hover:from-[#1ba1e2] hover:to-[#5bc0de] text-white font-bold py-4 rounded-xl transition-all duration-300 shadow-lg transform hover:-translate-y-1">
                Masuk ke Dashboard
            </button>
        </form>
    </div>
</body>
</html>