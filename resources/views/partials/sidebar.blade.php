<!-- Backdrop/Overlay Gelap khusus Layar HP -->
<div x-show="sidebarOpen" 
     @click="sidebarOpen = false" 
     x-transition.opacity 
     class="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm lg:hidden" 
     x-cloak>
</div>

<!-- SIDEBAR UTAMA -->
<aside :class="sidebarOpen ? 'translate-x-0 w-72' : '-translate-x-full w-0 lg:w-0 lg:-translate-x-full'" 
       class="fixed lg:relative inset-y-0 left-0 z-50 flex flex-col h-screen bg-slate-900 text-white transition-all duration-300 ease-in-out shadow-2xl shrink-0 overflow-hidden"
       x-data="{ 
           menuSearch: '',
           masterOpen: true, // Dropdown Master Data default terbuka
           evalOpen: true    // Dropdown Evaluasi default terbuka
       }">
    
    <div class="w-72 flex flex-col h-full">
        
        <!-- Header Sidebar (Logo) -->
        <div class="flex items-center justify-center h-20 border-b border-slate-800 shrink-0">
            <div class="bg-white p-1.5 rounded-full mr-3 shadow-[0_0_15px_rgba(255,255,255,0.2)]">
                <img src="{{ asset('storage/icon.png') }}" alt="Logo" class="h-8 w-8 object-contain">
            </div>
            <span class="text-xl font-extrabold tracking-wider text-white">FITONE</span>
        </div>

        <!-- Kotak Pencarian Menu (Live Search Sidebar) -->
        <div class="p-4 pb-2 border-b border-slate-800/60">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" x-model="menuSearch" placeholder="Cari menu..." class="w-full pl-9 pr-3 py-2 bg-slate-800/80 border border-slate-700/60 rounded-xl text-xs text-white placeholder-slate-400 focus:outline-none focus:border-blue-500 transition-all font-medium">
            </div>
        </div>

        <!-- Menu List -->
        <nav class="flex-1 p-4 space-y-2 overflow-y-auto custom-scrollbar">
            
            <!-- Kategori: Main Menu -->
<!-- 1. Manajemen Akun -->
<div x-show="!menuSearch || 'manajemen akun'.includes(menuSearch.toLowerCase())">
    <p class="px-2 text-[10px] font-bold tracking-widest text-slate-500 uppercase mb-2 mt-1">Main Menu</p>
    <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-4 py-3.5 {{ request()->routeIs('users.*') ? 'bg-blue-600 shadow-lg shadow-blue-900/50 text-white' : 'hover:bg-slate-800 text-slate-300' }} rounded-xl font-bold transition-all transform hover:-translate-y-0.5">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
        </svg>
        Manajemen Akun
    </a>
</div>

<!-- 2. Manajemen Event -->
<a href="{{ route('event.index') }}" class="flex items-center gap-3 px-4 py-3.5 mt-2 {{ request()->routeIs('event.*') ? 'bg-blue-600 shadow-lg text-white' : 'hover:bg-slate-800 text-slate-300' }} rounded-xl font-bold transition-all transform hover:-translate-y-0.5">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
    </svg>
    Manajemen Event
</a>

<!-- 3. Survey Kepuasan -->
<a href="{{ route('survey-kepuasan.rekap') }}" class="flex items-center gap-3 px-4 py-3.5 mt-2 {{ request()->routeIs('survey-kepuasan.*') ? 'bg-blue-600 shadow-lg text-white' : 'hover:bg-slate-800 text-slate-300' }} rounded-xl font-bold transition-all transform hover:-translate-y-0.5">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
    </svg>
    Survey Kepuasan
</a>

<!-- 4. Pengaduan (SIMPEL) -->
<a href="{{ route('pengaduan.index') }}" class="flex items-center gap-3 px-4 py-3.5 mt-2 {{ request()->routeIs('pengaduan.*') ? 'bg-blue-600 shadow-lg text-white' : 'hover:bg-slate-800 text-slate-300' }} rounded-xl font-bold transition-all transform hover:-translate-y-0.5">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
    </svg>
    Pengaduan Pelatihan
</a>
            <!-- Kategori: Master Data (Dropdown) -->
            <div class="pt-2">
                <button @click="masterOpen = !masterOpen" class="w-full flex items-center justify-between px-2 text-[10px] font-bold tracking-widest text-slate-400 uppercase mb-2 hover:text-white transition-colors">
                    <span>Master Data</span>
                    <svg class="w-4 h-4 transform transition-transform duration-200" :class="masterOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <div x-show="masterOpen" x-transition.origin.top class="space-y-1.5 pl-2 border-l-2 border-slate-800 ml-2">
                    
                    <!-- Master Pelatihan -->
                    <div x-show="!menuSearch || 'master pelatihan'.includes(menuSearch.toLowerCase())">
                        <a href="{{ route('master.pelatihan.index') }}" class="flex items-center gap-3 px-3.5 py-3 {{ request()->routeIs('master.pelatihan.*') ? 'bg-blue-600 shadow-lg shadow-blue-900/50 text-white' : 'hover:bg-slate-800 text-slate-300' }} rounded-xl font-semibold text-sm transition-all">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            Master Pelatihan
                        </a>
                    </div>

                    <!-- Master Fasilitator -->
                    <div x-show="!menuSearch || 'master fasilitator'.includes(menuSearch.toLowerCase())">
                        <a href="{{ route('master.fasilitator.index') }}" class="flex items-center gap-3 px-3.5 py-3 {{ request()->routeIs('master.fasilitator.*') ? 'bg-blue-600 shadow-lg shadow-blue-900/50 text-white' : 'hover:bg-slate-800 text-slate-300' }} rounded-xl font-semibold text-sm transition-all">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            Master Fasilitator
                        </a>
                    </div>

                    <!-- Master Skill Materi -->
                    <div x-show="!menuSearch || 'master skill materi'.includes(menuSearch.toLowerCase())">
                        <a href="{{ route('master.skill-materi.index') }}" class="flex items-center gap-3 px-3.5 py-3 {{ request()->routeIs('master.skill-materi.*') ? 'bg-blue-600 shadow-lg shadow-blue-900/50 text-white' : 'hover:bg-slate-800 text-slate-300' }} rounded-xl font-semibold text-sm transition-all">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                            Master Skill Materi
                        </a>
                    </div>

                </div>
            </div>

            <!-- Kategori: Evaluasi / Penilaian (Dropdown) -->
            <div class="pt-2">
                <button @click="evalOpen = !evalOpen" class="w-full flex items-center justify-between px-2 text-[10px] font-bold tracking-widest text-slate-400 uppercase mb-2 hover:text-white transition-colors">
                    <span>Data Evaluasi</span>
                    <svg class="w-4 h-4 transform transition-transform duration-200" :class="evalOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <div x-show="evalOpen" x-transition.origin.top class="space-y-1.5 pl-2 border-l-2 border-slate-800 ml-2">

                    <!-- Evaluasi Pelatihan -->
                    <div x-show="!menuSearch || 'evaluasi pelatihan'.includes(menuSearch.toLowerCase())">
                        <a href="{{ route('master.evaluasi-pelatihan.index') }}" class="flex items-center gap-3 px-3.5 py-3 {{ request()->routeIs('master.evaluasi-pelatihan.*') ? 'bg-blue-600 shadow-lg shadow-blue-900/50 text-white' : 'hover:bg-slate-800 text-slate-300' }} rounded-xl font-semibold text-sm transition-all">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            Evaluasi Pelatihan
                        </a>
                    </div>

                    <!-- Evaluasi Materi -->
                    <div x-show="!menuSearch || 'evaluasi materi'.includes(menuSearch.toLowerCase())">
                        <a href="{{ route('master.evaluasi-materi.index') }}" class="flex items-center gap-3 px-3.5 py-3 {{ request()->routeIs('master.evaluasi-materi.*') ? 'bg-blue-600 shadow-lg shadow-blue-900/50 text-white' : 'hover:bg-slate-800 text-slate-300' }} rounded-xl font-semibold text-sm transition-all">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                            Evaluasi Materi
                        </a>
                    </div>

                    <!-- Evaluasi Fasilitator -->
                    <div x-show="!menuSearch || 'evaluasi fasilitator'.includes(menuSearch.toLowerCase())">
                        <a href="{{ route('master.evaluasi-fasilitator.index') }}" class="flex items-center gap-3 px-3.5 py-3 {{ request()->routeIs('master.evaluasi-fasilitator.*') ? 'bg-blue-600 shadow-lg shadow-blue-900/50 text-white' : 'hover:bg-slate-800 text-slate-300' }} rounded-xl font-semibold text-sm transition-all">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            Evaluasi Fasilitator
                        </a>
                    </div>

                </div>
            </div>

        </nav>

        <!-- Footer Sidebar (Logout dengan Swal) -->
        <div class="p-5 border-t border-slate-800 shrink-0 bg-slate-900">
            <form id="logout-form" action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="button" onclick="confirmLogout()" class="flex items-center justify-center w-full gap-2 px-4 py-3 text-red-400 hover:text-white bg-red-500/10 hover:bg-red-500 rounded-xl transition-all duration-300 font-bold border border-red-500/20 hover:border-transparent">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Keluar
                </button>
            </form>
        </div>
        
    </div>
</aside>

<!-- Script SweetAlert2 untuk Konfirmasi Logout -->
<script>
    function confirmLogout() {
        Swal.fire({
            title: 'Apakah Anda Yakin?',
            text: "Anda akan keluar dari sesi ini dan harus login kembali.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#475569',
            confirmButtonText: 'Ya, Keluar!',
            cancelButtonText: 'Batal',
            backdrop: `rgba(0,0,0,0.6)`,
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-xl px-6 py-2.5 font-bold',
                cancelButton: 'rounded-xl px-6 py-2.5 font-bold'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            }
        })
    }
</script>