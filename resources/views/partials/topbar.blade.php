<!-- TOPBAR UTAMA -->
<header class="flex items-center justify-between h-20 px-4 md:px-8 bg-white border-b border-gray-200 shadow-sm shrink-0 z-30 relative">
    
    <!-- Kiri: Tombol Toggle & Judul -->
    <div class="flex items-center gap-4">
        <!-- Tombol Toggle (Bisa diklik di HP maupun Laptop) -->
        <button @click="sidebarOpen = !sidebarOpen" class="p-2.5 text-gray-500 bg-gray-50 hover:bg-blue-500 hover:text-white rounded-xl transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-blue-500/50 shadow-sm border border-gray-100">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>
        <h1 class="text-xl md:text-2xl font-extrabold text-[#1a365d] hidden sm:block"></h1>
    </div>

    <!-- Kanan: Notifikasi & Profil -->
    <div class="flex items-center gap-3 md:gap-6">
        
        <!-- Dropdown Notifikasi -->
        <div class="relative" x-data="{ notifOpen: false }" @click.outside="notifOpen = false">
            
            <button @click="notifOpen = !notifOpen" class="relative p-2.5 text-gray-500 hover:text-blue-600 bg-gray-50 rounded-full transition-all duration-300 border border-gray-200 shadow-sm focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                <!-- Dot Merah Ping! -->
                <span class="absolute top-1 right-1 w-3.5 h-3.5 bg-red-500 border-2 border-white rounded-full flex animate-bounce"></span>
            </button>

            <!-- Kotak Notifikasi -->
            <div x-show="notifOpen" 
                 x-transition.origin.top.right 
                 class="absolute right-0 mt-4 w-72 md:w-80 bg-white rounded-2xl shadow-[0_10px_40px_rgba(0,0,0,0.1)] border border-gray-100 overflow-hidden z-50" 
                 x-cloak>
                <div class="px-5 py-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800">Notifikasi</h3>
                    <span class="text-[10px] font-bold bg-blue-500 text-white px-2 py-1 rounded-full">Baru</span>
                </div>
                <div class="p-4 hover:bg-gray-50 cursor-pointer border-b border-gray-50 transition-colors">
                    <p class="text-sm font-bold text-gray-800">Sistem Berjalan Baik 🚀</p>
                    <p class="text-xs text-gray-500 mt-1">Layout responsif sudah berfungsi dengan sempurna.</p>
                </div>
            </div>
        </div>

        <!-- Profil User -->
        <div class="flex items-center gap-3 pl-3 md:pl-5 border-l border-gray-200">
            <div class="text-right hidden md:block">
                <p class="text-sm font-extrabold text-[#1a365d]">{{ auth()->user()->name ?? 'Alfa' }}</p>
                <p class="text-[11px] font-bold text-blue-500 uppercase tracking-wider">Super Admin</p>
            </div>
            <!-- Avatar -->
            <div class="w-10 h-10 md:w-11 md:h-11 bg-gradient-to-br from-[#1a365d] to-[#1ba1e2] rounded-full flex items-center justify-center text-white text-lg font-bold shadow-md shadow-blue-500/30 border-2 border-white shrink-0">
                {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
            </div>
        </div>

    </div>
</header>