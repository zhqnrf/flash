<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluasi Fasilitator - {{ $fasilitator->nama_fasilitator }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        input[type=radio] { position: absolute; opacity: 0; width: 0; height: 0; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen text-slate-800 pb-14">

    @if(session('success'))
    <script>document.addEventListener('DOMContentLoaded', () => Swal.fire({icon: 'success', title: 'Terima Kasih!', text: "{{ session('success') }}", confirmButtonColor: '#1a365d'}));</script>
    @endif
    @if(session('error'))
    <script>document.addEventListener('DOMContentLoaded', () => Swal.fire({icon: 'info', title: 'Info', text: "{{ session('error') }}", confirmButtonColor: '#1a365d'}));</script>
    @endif
    @if($errors->any())
    <script>document.addEventListener('DOMContentLoaded', () => Swal.fire({icon: 'error', title: 'Periksa Kembali', html: `{!! implode('<br>', $errors->all()) !!}`, confirmButtonColor: '#1a365d'}));</script>
    @endif

    <div class="max-w-lg mx-auto bg-white shadow-xl mt-0 md:mt-10 rounded-none md:rounded-[2rem] overflow-hidden border border-slate-100">

        <div class="bg-gradient-to-br from-[#1a365d] to-[#0f2942] p-8 text-white">
            <span class="bg-blue-400 text-xs font-bold px-2.5 py-1 rounded-full text-white">Evaluasi Fasilitator</span>
            <h1 class="text-xl md:text-2xl font-extrabold mt-3">{{ $fasilitator->nama_fasilitator }}</h1>
            @if($materi)
            <p class="text-sm text-white/70 mt-1">Materi: {{ $materi }}</p>
            @endif
            <p class="text-xs text-white/50 mt-1">{{ $event->nama_event }}</p>
        </div>

        <div class="p-6 md:p-8"
             x-data="{
                pesertas: {{ $pesertas->values() }},
                search: '',
                selected: null,
                get filtered() {
                    if (this.search.trim() === '') return [];
                    const q = this.search.toLowerCase();
                    return this.pesertas.filter(p => p.nama.toLowerCase().includes(q)).slice(0, 8);
                },
                pilih(p) { this.selected = p; this.search = ''; }
             }"
             x-init="
                const initId = {{ $initialRegistrasiId ? (int) $initialRegistrasiId : 'null' }};
                if (initId) { const found = pesertas.find(p => p.id === initId); if (found) selected = found; }
             ">

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
                                <span x-show="p.sudah_isi" class="text-[10px] font-bold bg-emerald-100 text-emerald-700 px-2 py-1 rounded-full">Sudah Isi</span>
                            </button>
                        </template>
                        <p x-show="filtered.length === 0" class="text-xs text-slate-400 text-center py-4">Nama tidak ditemukan. Pastikan Anda sudah terdaftar & diterima sebagai peserta.</p>
                    </div>
                </div>
            </template>

            <!-- STEP 2: FORM EVALUASI -->
            <template x-if="selected">
                <div>
                    <div class="bg-blue-50/60 p-4 rounded-2xl border border-blue-100 mb-6 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-bold text-blue-700 uppercase tracking-wide">Mengisi sebagai</p>
                            <p class="text-base font-extrabold text-[#1a365d]" x-text="selected.nama"></p>
                        </div>
                        <button type="button" @click="selected = null" class="text-xs font-bold text-slate-400 hover:text-red-500">Ganti</button>
                    </div>

                    <template x-for="p in [selected]" :key="p.id">
                        <template x-if="p.sudah_isi">
                            <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-6 text-center">
                                <svg class="w-10 h-10 text-emerald-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p class="font-extrabold text-emerald-800">Anda Sudah Mengevaluasi Fasilitator Ini</p>
                                <p class="text-sm text-emerald-700 mt-1">Terima kasih atas partisipasi Anda!</p>
                            </div>
                        </template>
                    </template>

                    <form x-show="!selected.sudah_isi" action="{{ route('evaluasi-fasilitator.store', [$event->uuid, $fasilitator->id]) }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="registrasi_id" :value="selected.id">

                        @forelse($kriteria as $k)
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <p class="text-sm font-bold text-slate-700 mb-3">{{ $loop->iteration }}. {{ $k->nama_evaluasi }}</p>
                            <div class="flex gap-2 flex-wrap">
                                @for($i = $k->rentang_nilai_min; $i <= $k->rentang_nilai_max; $i++)
                                <label class="cursor-pointer">
                                    <input type="radio" name="nilai[{{ $k->id }}]" value="{{ $i }}" required class="sr-only peer">
                                    <div class="w-11 h-11 flex items-center justify-center rounded-xl border-2 border-slate-200 font-extrabold text-slate-500 bg-white peer-checked:bg-[#1a365d] peer-checked:border-[#1a365d] peer-checked:text-white transition">{{ $i }}</div>
                                </label>
                                @endfor
                            </div>
                            <div class="flex justify-between text-[10px] text-slate-400 mt-1.5 px-1">
                                <span>Sangat Kurang</span><span>Sangat Baik</span>
                            </div>
                        </div>
                        @empty
                        <p class="text-xs text-slate-400 bg-slate-50 p-4 rounded-xl border border-slate-100">Belum ada kriteria evaluasi fasilitator.</p>
                        @endforelse

                        @if(count($kriteria))
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <label class="block text-sm font-bold text-slate-700 mb-2">Saran / Masukan untuk Fasilitator Ini <span class="text-slate-400 font-normal">(Opsional)</span></label>
                            <textarea name="saran" rows="3" placeholder="Tuliskan saran, kritik, atau masukan Anda di sini..." class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm font-medium transition"></textarea>
                        </div>

                        <button type="submit" class="w-full bg-[#1a365d] hover:bg-[#142c4c] text-white py-4 rounded-xl font-extrabold text-sm shadow-xl transition-all transform hover:-translate-y-0.5">
                            Kirim Evaluasi Fasilitator
                        </button>
                        @endif
                    </form>

                    <a href="{{ route('evaluasi-pelatihan.public', $event->uuid) }}" class="block text-center text-xs font-bold text-slate-400 hover:text-[#1a365d] mt-5">← Kembali ke Evaluasi Pelatihan</a>
                </div>
            </template>
        </div>
    </div>
</body>
</html>