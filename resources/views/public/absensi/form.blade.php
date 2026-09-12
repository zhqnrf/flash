<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi - {{ $event->nama_event }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .dropzone { border: 2px dashed #cbd5e1; transition: all .2s ease; }
        .dropzone:hover { border-color: #1a365d; background-color: #f0f5fb; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen text-slate-800 pb-14">

    @if(session('success'))
    <script>document.addEventListener('DOMContentLoaded', () => Swal.fire({icon: 'success', title: 'Selamat!', text: "{{ session('success') }}", confirmButtonColor: '#1a365d'}));</script>
    @endif
    @if(session('error'))
    <script>document.addEventListener('DOMContentLoaded', () => Swal.fire({icon: 'error', title: 'Gagal', text: "{{ session('error') }}", confirmButtonColor: '#1a365d'}));</script>
    @endif
    @if($errors->any())
    <script>document.addEventListener('DOMContentLoaded', () => Swal.fire({icon: 'error', title: 'Periksa Kembali', html: `{!! implode('<br>', $errors->all()) !!}`, confirmButtonColor: '#1a365d'}));</script>
    @endif

    <div class="max-w-lg mx-auto bg-white shadow-xl mt-0 md:mt-10 rounded-none md:rounded-[2rem] overflow-hidden border border-slate-100">

        <div class="bg-gradient-to-br from-[#1a365d] to-[#0f2942] p-8 text-white">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="bg-teal-400 text-xs font-bold px-2.5 py-1 rounded-full text-white">Absensi Kehadiran</span>
                @if($hariKe)
                <span class="bg-white/20 text-xs font-bold px-2.5 py-1 rounded-full text-white">Hari ke-{{ $hariKe }} dari {{ $totalHari }}</span>
                @endif
            </div>
            <h1 class="text-xl md:text-2xl font-extrabold mt-3">{{ $event->nama_event }}</h1>
            <p class="text-sm text-white/70 mt-1">
                {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }} &middot;
                Presensi {{ \Carbon\Carbon::parse($event->waktu_presensi_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($event->waktu_presensi_selesai)->format('H:i') }} WIB
            </p>
        </div>

        @if(!$bukaAbsensi)
        <div class="p-8 text-center">
            <div class="bg-amber-50 border border-amber-100 rounded-2xl p-6">
                <svg class="w-10 h-10 text-amber-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="font-extrabold text-amber-800">Absensi Belum / Sudah Tidak Bisa Diakses</p>
                <p class="text-sm text-amber-700 mt-1">Absensi hanya bisa dilakukan pada rentang tanggal dan jam presensi yang telah ditentukan panitia.</p>
            </div>
        </div>
        @else
        <div class="p-6 md:p-8"
             x-data="{
                pesertas: {{ $pesertas->map(fn($p) => ['id' => $p->id, 'nama' => $p->nama_lengkap, 'instansi' => $p->instansi, 'sudah_absen' => (bool) $p->absensi, 'jam' => $p->absensi ? $p->absensi->jam_masuk->format('H:i') : null])->values() }},
                search: '',
                selected: null,
                fileName: '', previewUrl: '',
                get filtered() {
                    if (this.search.trim() === '') return [];
                    const q = this.search.toLowerCase();
                    return this.pesertas.filter(p => p.nama.toLowerCase().includes(q)).slice(0, 8);
                },
                pilih(p) { this.selected = p; this.search = ''; },
                handleFile(e) {
                    const f = e.target.files[0];
                    if (!f) { this.fileName=''; this.previewUrl=''; return; }
                    this.fileName = f.name;
                    this.previewUrl = URL.createObjectURL(f);
                }
             }">

            <!-- STEP 1: CARI & PILIH NAMA -->
            <template x-if="!selected">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2">Cari Nama Anda</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" x-model="search" placeholder="Ketik nama Anda..." class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm font-medium">
                    </div>

                    <div class="mt-3 space-y-2" x-show="search.trim() !== ''">
                        <template x-for="p in filtered" :key="p.id">
                            <button type="button" @click="pilih(p)" class="w-full text-left p-3.5 bg-white border border-slate-200 rounded-xl hover:border-blue-400 hover:bg-blue-50/50 transition flex items-center justify-between">
                                <span>
                                    <span class="block text-sm font-extrabold text-slate-800" x-text="p.nama"></span>
                                    <span class="block text-xs text-slate-400" x-text="p.instansi"></span>
                                </span>
                                <span x-show="p.sudah_absen" class="text-[10px] font-bold bg-emerald-100 text-emerald-700 px-2 py-1 rounded-full">Sudah Absen</span>
                            </button>
                        </template>
                        <p x-show="filtered.length === 0" class="text-xs text-slate-400 text-center py-4">Nama tidak ditemukan. Pastikan Anda sudah terdaftar & diterima sebagai peserta.</p>
                    </div>
                </div>
            </template>

            <!-- STEP 2: SELFIE & SUBMIT -->
            <template x-if="selected">
                <div>
                    <div class="bg-blue-50/60 p-4 rounded-2xl border border-blue-100 mb-6 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-bold text-blue-700 uppercase tracking-wide">Absensi atas nama</p>
                            <p class="text-base font-extrabold text-[#1a365d]" x-text="selected.nama"></p>
                        </div>
                        <button type="button" @click="selected = null" class="text-xs font-bold text-slate-400 hover:text-red-500">Ganti</button>
                    </div>

                    <template x-if="selected.sudah_absen">
                        <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-6 text-center">
                            <svg class="w-10 h-10 text-emerald-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="font-extrabold text-emerald-800">Anda Sudah Absen Hari Ini</p>
                            <p class="text-sm text-emerald-700 mt-1">Tercatat masuk pukul <span x-text="selected.jam"></span> WIB. Silakan absen lagi besok jika pelatihan masih berlanjut.</p>
                        </div>
                    </template>

                    <template x-if="!selected.sudah_absen">
                        <form action="{{ route('presensi.store', $event->uuid) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                            @csrf
                            <input type="hidden" name="registrasi_id" :value="selected.id">

                            <div>
                                <label class="block text-xs font-bold text-slate-600 mb-1.5">Foto Selfie <span class="text-red-500">*</span></label>
                                <p class="text-xs text-slate-400 mb-3">Ambil foto selfie menggunakan kamera depan — pastikan wajah Anda dan suasana acara terlihat jelas.</p>

                                <label for="foto_selfie" class="dropzone flex flex-col items-center justify-center gap-2 rounded-2xl px-6 py-8 cursor-pointer text-center overflow-hidden" :class="fileName ? 'border-emerald-400 bg-emerald-50/60 p-0' : ''">
                                    <input id="foto_selfie" type="file" name="foto_selfie" accept="image/*" capture="user" required class="hidden" @change="handleFile($event)">

                                    <template x-if="!fileName">
                                        <div class="flex flex-col items-center gap-2">
                                            <div class="bg-blue-100 p-3 rounded-full text-blue-700">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            </div>
                                            <p class="text-sm font-bold text-slate-700">Ketuk untuk ambil selfie</p>
                                            <p class="text-xs text-slate-400">Kamera depan akan terbuka otomatis</p>
                                        </div>
                                    </template>

                                    <template x-if="fileName">
                                        <div class="relative w-full">
                                            <img :src="previewUrl" class="w-full h-64 object-cover">
                                            <div class="absolute bottom-2 right-2 bg-white/90 text-[10px] font-bold text-emerald-700 px-2 py-1 rounded-full">Ketuk untuk ganti foto</div>
                                        </div>
                                    </template>
                                </label>
                            </div>

                            <button type="submit" class="w-full bg-[#1a365d] hover:bg-[#142c4c] text-white py-4 rounded-xl font-extrabold text-sm shadow-xl transition-all transform hover:-translate-y-0.5">
                                Kirim Absensi
                            </button>
                        </form>
                    </template>
                </div>
            </template>
        </div>
        @endif
    </div>
</body>
</html>