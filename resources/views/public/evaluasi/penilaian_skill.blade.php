<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penilaian Peserta - {{ $materi->nama_materi }}</title>
    
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

    <!-- Notifikasi SweetAlert -->
    @if(session('success'))
    <script>document.addEventListener('DOMContentLoaded', () => Swal.fire({icon: 'success', title: 'Tersimpan!', text: {!! json_encode(session('success')) !!}, confirmButtonColor: '#1a365d'}));</script>
    @endif
    @if(session('error'))
    <script>document.addEventListener('DOMContentLoaded', () => Swal.fire({icon: 'info', title: 'Info', text: {!! json_encode(session('error')) !!}, confirmButtonColor: '#1a365d'}));</script>
    @endif
    @if($errors->any())
    <script>document.addEventListener('DOMContentLoaded', () => Swal.fire({icon: 'error', title: 'Periksa Kembali', html: {!! json_encode(implode('<br>', $errors->all())) !!}, confirmButtonColor: '#1a365d'}));</script>
    @endif

    <div class="max-w-lg mx-auto bg-white shadow-xl mt-0 md:mt-10 rounded-none md:rounded-[2rem] overflow-hidden border border-slate-100">

        <!-- HEADER SECTION -->
        <div class="bg-gradient-to-br from-[#1a365d] to-[#0f2942] p-8 text-white">
            <span class="bg-amber-400 text-xs font-bold px-2.5 py-1 rounded-full text-[#1a365d]">Penilaian Peserta</span>
            <h1 class="text-xl md:text-2xl font-extrabold mt-3">{{ $materi->nama_materi }}</h1>
            <p class="text-sm text-white/70 mt-1">Fasilitator: {{ $fasilitator->nama_fasilitator }}</p>

            <!-- INFORMASI AMBANG BATAS & RENTANG NILAI -->
            <div class="mt-4 flex flex-wrap gap-2">
                <span class="bg-indigo-500/30 border border-indigo-400/50 text-indigo-100 text-xs font-bold px-3 py-1.5 rounded-lg backdrop-blur-sm flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Ambang Batas: {{ $materi->ambang_batas ?? 0 }}
                </span>
                <span class="bg-emerald-500/30 border border-emerald-400/50 text-emerald-100 text-xs font-bold px-3 py-1.5 rounded-lg backdrop-blur-sm flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    Skala: {{ $materi->rentang_nilai_min }} - {{ $materi->rentang_nilai_max }}
                </span>
            </div>

            <div class="mt-5 bg-white/10 rounded-xl p-3.5 flex items-center justify-between backdrop-blur-sm border border-white/10">
                <div>
                    <p class="text-[10px] text-white/60 uppercase tracking-wide font-bold">Progres Penilaian</p>
                    <p class="text-lg font-extrabold">{{ $sudahLengkapCount }} / {{ $totalPeserta }} peserta selesai</p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] text-white/60 uppercase tracking-wide font-bold">Sisa</p>
                    <p class="text-lg font-extrabold text-amber-300">{{ $sisaPeserta }} peserta</p>
                </div>
            </div>
        </div>

        <!-- ALPINE JS DATA & LOGIC -->
        <div class="p-6 md:p-8"
             x-data="{
                pesertas: {{ $pesertas->values() }},
                skills: {{ $skills->map(fn($s) => ['id' => $s->id, 'nama' => $s->nama_skill]) }},
                totalSkill: {{ (int) $totalSkill }},
                sudahDinilaiMap: {{ $sudahDinilaiMap->toJson() ?: '{}' }},
                
                search: '',
                selected: null,
                formValues: {}, // <-- Menampung klik per skill

                get filtered() {
                    if (this.search.trim() === '') return [];
                    const q = this.search.toLowerCase();
                    return this.pesertas.filter(p => p.nama.toLowerCase().includes(q)).slice(0, 8);
                },
                dinilaiCount(id) { return (this.sudahDinilaiMap[id] || []).length; },
                
                pilih(p) { 
                    this.selected = p; 
                    this.search = ''; 
                    this.formValues = {}; 
                },
                
                get pendingSkillIds() {
                    if (!this.selected) return [];
                    const done = this.sudahDinilaiMap[this.selected.id] || [];
                    return this.skills.filter(s => !done.includes(s.id)).map(s => s.id);
                },
                get sudahLengkap() {
                    if (!this.selected) return false;
                    return this.totalSkill > 0 && this.dinilaiCount(this.selected.id) >= this.totalSkill;
                },

                // ==========================================
                // LOGIKA KALKULATOR TAHAN BANTING (BANYAK SKILL)
                // ==========================================
                get totalPoinAktif() {
                    let total = 0;
                    for (let key in this.formValues) {
                        if(this.formValues[key]) total += parseInt(this.formValues[key]);
                    }
                    return total;
                },
                get dijawabCount() {
                    return Object.keys(this.formValues).filter(k => this.formValues[k]).length;
                },
                get nilaiRealtimeSkala100() {
                    // Jika belum ada yang dijawab, tampilkan 0
                    if (this.dijawabCount === 0) return 0;
                    
                    // Maksimal poin dihitung HANYA dari jumlah skill yang SUDAH DIKLIK
                    const currentMax = this.dijawabCount * {{ (int) $materi->rentang_nilai_max }};
                    
                    // Rumus: (Total Poin / Max Poin Sementara) * 100
                    return Math.round((this.totalPoinAktif / currentMax) * 100);
                },
                get isLulus() {
                    return this.nilaiRealtimeSkala100 >= {{ (float) ($materi->ambang_batas ?? 0) }};
                }
             }"
             x-init="
                const initId = {{ $initialRegistrasiId ? (int) $initialRegistrasiId : 'null' }};
                if (initId) { const found = pesertas.find(p => p.id === initId); if (found) selected = found; }
             ">

            <!-- STEP 1: CARI & PILIH PESERTA -->
            <template x-if="!selected">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2">Cari Nama Peserta yang Akan Dinilai</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" x-model="search" placeholder="Ketik nama peserta..." class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm font-medium">
                    </div>
                    <div class="mt-3 space-y-2" x-show="search.trim() !== ''">
                        <template x-for="p in filtered" :key="p.id">
                            <button type="button" @click="pilih(p)" class="w-full text-left p-3.5 bg-white border border-slate-200 rounded-xl hover:border-blue-400 hover:bg-blue-50/50 transition flex items-center justify-between">
                                <span>
                                    <span class="block text-sm font-extrabold text-slate-800" x-text="p.nama"></span>
                                    <span class="block text-xs text-slate-400" x-text="p.instansi"></span>
                                </span>
                                <span class="text-[10px] font-bold px-2 py-1 rounded-full"
                                      :class="dinilaiCount(p.id) >= totalSkill && totalSkill > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'"
                                      x-text="(dinilaiCount(p.id) >= totalSkill && totalSkill > 0) ? 'Selesai' : dinilaiCount(p.id) + '/' + totalSkill"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </template>

            <!-- STEP 2: FORM PENILAIAN SKILL -->
            <template x-if="selected">
                <div>
                    <div class="bg-blue-50/60 p-4 rounded-2xl border border-blue-100 mb-6 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-bold text-blue-700 uppercase tracking-wide">Menilai Peserta</p>
                            <p class="text-base font-extrabold text-[#1a365d]" x-text="selected.nama"></p>
                        </div>
                        <button type="button" @click="selected = null" class="text-xs font-bold text-slate-400 hover:text-red-500">Ganti</button>
                    </div>

                    <template x-if="sudahLengkap">
                        <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-5 text-center mb-4">
                            <svg class="w-8 h-8 text-emerald-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-sm font-extrabold text-emerald-800">Peserta Ini Sudah Selesai Dinilai</p>
                        </div>
                    </template>

                    <form x-show="!sudahLengkap" action="{{ route('penilaian-skill.store', [$event->uuid, $fasilitator->id, $materi->id]) }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="registrasi_id" :value="selected.id">

                        <!-- WIDGET REAL-TIME (Melayang) -->
                        <div class="sticky top-4 z-10 bg-white/95 backdrop-blur-md shadow-xl shadow-blue-900/10 border border-slate-200 rounded-2xl p-4 mb-6 flex items-center justify-between transition-all">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Rata-Rata Sementara</p>
                                <div class="flex items-end gap-1 mt-0.5">
                                    <span class="text-3xl font-black transition-colors" 
                                          :class="dijawabCount === 0 ? 'text-slate-300' : (isLulus ? 'text-emerald-600' : 'text-red-500')" 
                                          x-text="nilaiRealtimeSkala100"></span>
                                    <span class="text-sm font-bold text-slate-400 mb-1">/ 100</span>
                                </div>
                                <p class="text-[10px] font-medium text-slate-500 mt-1">Terisi <span class="font-bold text-blue-600" x-text="dijawabCount"></span> dari <span x-text="pendingSkillIds.length"></span> indikator</p>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] font-bold px-2.5 py-1.5 rounded-md transition-colors" 
                                      :class="dijawabCount === 0 ? 'bg-slate-100 text-slate-400' : (isLulus ? 'bg-emerald-100 text-emerald-700' : 'bg-red-50 text-red-600')"
                                      x-text="dijawabCount === 0 ? 'Belum Ada Nilai' : (isLulus ? 'Lulus Ambang Batas' : 'Belum Lulus')">
                                </span>
                                <p class="text-[10px] text-slate-400 mt-2 font-medium">Batas Lulus: {{ $materi->ambang_batas }}</p>
                            </div>
                        </div>

                        <!-- LIST SKILL YANG PANJANG -->
                        @foreach($skills as $skill)
                        <div x-show="pendingSkillIds.includes({{ $skill->id }})" class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <p class="text-sm font-bold text-slate-700 mb-3">{{ $loop->iteration }}. {{ $skill->nama_skill }}</p>
                            <div class="flex gap-2 flex-wrap">
                                @for($i = $materi->rentang_nilai_min; $i <= $materi->rentang_nilai_max; $i++)
                                <label class="cursor-pointer">
                                    <input type="radio" x-model="formValues[{{ $skill->id }}]" name="nilai[{{ $skill->id }}]" value="{{ $i }}" x-bind:required="pendingSkillIds.includes({{ $skill->id }})" class="sr-only peer">
                                    <div class="w-11 h-11 flex items-center justify-center rounded-xl border-2 border-slate-200 font-extrabold text-slate-500 bg-white peer-checked:bg-[#1a365d] peer-checked:border-[#1a365d] peer-checked:text-white transition">
                                        {{ $i }}
                                    </div>
                                </label>
                                @endfor
                            </div>
                        </div>
                        @endforeach

                        @if($skills->isNotEmpty())
                        <!-- Tombol Submit dengan validasi visual -->
                        <button type="submit" 
                                class="w-full py-4 rounded-xl font-extrabold text-sm shadow-xl transition-all transform hover:-translate-y-0.5 mt-4"
                                :class="dijawabCount === pendingSkillIds.length ? 'bg-[#1a365d] hover:bg-[#142c4c] text-white' : 'bg-slate-300 text-slate-500 cursor-not-allowed'"
                                :disabled="dijawabCount !== pendingSkillIds.length">
                            <span x-text="dijawabCount === pendingSkillIds.length ? 'Simpan Nilai Peserta Ini' : 'Selesaikan Penilaian (' + dijawabCount + '/' + pendingSkillIds.length + ')' "></span>
                        </button>
                        @endif
                    </form>
                </div>
            </template>
        </div>
    </div>
</body>
</html>