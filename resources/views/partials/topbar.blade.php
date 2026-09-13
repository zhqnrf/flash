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
        <div class="relative"
             x-data="notifikasiApp()"
             x-init="init()"
             @click.outside="notifOpen = false">

            <button @click="toggleOpen()" class="relative p-2.5 text-gray-500 hover:text-blue-600 bg-gray-50 rounded-full transition-all duration-300 border border-gray-200 shadow-sm focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                <!-- Dot Merah Ping! (hanya muncul kalau ada yang belum dibaca) -->
                <span x-show="unreadCount > 0" x-cloak class="absolute top-1 right-1 min-w-[16px] h-[16px] px-[3px] bg-red-500 border-2 border-white rounded-full flex items-center justify-center animate-bounce">
                    <span class="text-[9px] leading-none font-bold text-white" x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
                </span>
            </button>

            <!-- Kotak Notifikasi -->
            <div x-show="notifOpen"
                 x-transition.origin.top.right
                 class="absolute right-0 mt-4 w-80 md:w-96 bg-white rounded-2xl shadow-[0_10px_40px_rgba(0,0,0,0.1)] border border-gray-100 overflow-hidden z-50"
                 x-cloak>

                <!-- Header -->
                <div class="px-5 py-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800">Notifikasi</h3>
                    <button @click="bacaSemua()" type="button" class="text-[11px] font-bold text-blue-500 hover:text-blue-700">Tandai semua dibaca</button>
                </div>

                <!-- Search -->
                <div class="p-3 bg-gray-50 border-b border-gray-100">
                    <div class="relative">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"></path></svg>
                        <input type="text" x-model="search" @input.debounce.400ms="muat()"
                               placeholder="Cari notifikasi..."
                               class="w-full text-sm pl-9 pr-3 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/40">
                    </div>
                </div>

                <!-- Daftar Notifikasi -->
                <div class="max-h-96 overflow-y-auto">

                    <!-- Loading -->
                    <div x-show="loading" class="p-6 text-center text-xs text-gray-400">Memuat...</div>

                    <!-- Kosong -->
                    <template x-if="!loading && reminders.length === 0 && items.length === 0">
                        <div class="p-6 text-center text-xs text-gray-400">Tidak ada notifikasi.</div>
                    </template>

                    <!-- Reminder: Perlu Tindakan (fasilitator belum isi, dll) -->
                    <template x-if="reminders.length > 0">
                        <div>
                            <p class="px-5 pt-3 pb-1 text-[10px] font-bold text-amber-600 uppercase tracking-wider">Perlu Tindakan</p>
                            <template x-for="item in reminders" :key="item.id">
                                <a :href="item.link || '#'" class="block p-4 hover:bg-amber-50 cursor-pointer border-b border-gray-50 transition-colors">
                                    <p class="text-sm font-bold text-gray-800" x-text="item.judul"></p>
                                    <p class="text-xs text-gray-500 mt-1" x-text="item.pesan"></p>
                                    <p class="text-[10px] text-gray-400 mt-1" x-text="item.waktu"></p>
                                </a>
                            </template>
                        </div>
                    </template>

                    <!-- Notifikasi Aktivitas -->
                    <template x-if="items.length > 0">
                        <div>
                            <p class="px-5 pt-3 pb-1 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Aktivitas</p>
                            <template x-for="item in items" :key="item.id">
                                <a :href="'{{ url('notifikasi') }}/' + item.id + '/baca'"
                                   class="block p-4 hover:bg-gray-50 cursor-pointer border-b border-gray-50 transition-colors"
                                   :class="!item.is_read ? 'bg-blue-50/40' : ''">
                                    <div class="flex items-start gap-2">
                                        <span x-show="!item.is_read" class="mt-1.5 w-2 h-2 rounded-full bg-blue-500 shrink-0"></span>
                                        <div class="min-w-0">
                                            <p class="text-sm truncate" :class="item.is_read ? 'font-semibold text-gray-600' : 'font-bold text-gray-800'" x-text="item.judul"></p>
                                            <p class="text-xs text-gray-500 mt-1" x-text="item.pesan"></p>
                                            <p class="text-[10px] text-gray-400 mt-1" x-text="item.waktu"></p>
                                        </div>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </template>

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

<script>
function notifikasiApp() {
    return {
        notifOpen: false,
        loading: false,
        search: '',
        unreadCount: 0,
        items: [],
        reminders: [],

        init() {
            this.muat();
            // Perbarui badge tiap 30 detik walau dropdown tertutup
            setInterval(() => this.muat(), 30000);
        },

        toggleOpen() {
            this.notifOpen = !this.notifOpen;
            if (this.notifOpen) this.muat();
        },

        muat() {
            this.loading = true;
            const url = new URL('{{ route('notifikasi.index') }}', window.location.origin);
            if (this.search) url.searchParams.set('search', this.search);

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.json())
                .then(data => {
                    this.items = data.notifikasis || [];
                    this.reminders = data.reminder_fasilitator || [];
                    this.unreadCount = data.unread_count || 0;
                })
                .catch(() => {})
                .finally(() => { this.loading = false; });
        },

        bacaSemua() {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch('{{ route('notifikasi.baca-semua') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                }
            }).then(() => this.muat());
        }
    }
}
</script>
