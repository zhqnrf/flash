@extends('layouts.admin')
@section('title', 'Penilaian Peserta - ' . $event->nama_event)

@section('content')

@if(session('success'))
<script>document.addEventListener('DOMContentLoaded', () => Swal.fire({icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", timer: 2200, showConfirmButton: false, customClass: {popup: 'rounded-2xl'}}));</script>
@endif

<!-- Tambahkan di atas script Alpine js kamu -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
<div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-end gap-4">
    <div>
        <a href="{{ route('event.index') }}" class="text-xs font-bold text-slate-400 hover:text-[#1a365d] flex items-center gap-1 mb-2">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Daftar Event
        </a>
        <h2 class="text-3xl font-extrabold text-[#1a365d]">Dashboard Penilaian</h2>
        <p class="text-gray-500 text-sm mt-1">{{ $event->nama_event }} — {{ optional($event->pelatihan)->nama_pelatihan }}</p>
    </div>
</div>

<!-- ============ STATISTIK / DASHBOARD ============ -->
<div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Peserta</p>
        <p class="text-2xl font-extrabold text-slate-800 mt-1">{{ $totalPeserta }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Selesai Nilai</p>
        <p class="text-2xl font-extrabold text-emerald-600 mt-1">{{ $totalSudahLengkap }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Belum Lengkap</p>
        <p class="text-2xl font-extrabold text-amber-500 mt-1">{{ $totalBelumLengkap }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Lulus</p>
        <p class="text-2xl font-extrabold text-indigo-600 mt-1">{{ $totalLulus }}</p>
    </div>
    
    <!-- CARD PENGINTAI FASILITATOR -->
    <div class="col-span-2 bg-gradient-to-r from-[#1a365d] to-[#0f2942] rounded-2xl shadow-sm border border-[#1a365d] p-4 cursor-pointer hover:shadow-lg transition transform hover:-translate-y-1 relative overflow-hidden" onclick="showProgressFasilitator()">
        <div class="absolute right-0 top-0 opacity-10"><svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg></div>
        <p class="text-[10px] font-bold text-blue-200 uppercase tracking-wide relative z-10">Pantau Fasilitator</p>
        <p class="text-lg font-extrabold text-white mt-0.5 relative z-10">Cek Progres Penilaian &rarr;</p>
        <p class="text-[11px] text-blue-100 mt-1 relative z-10">Klik untuk melihat siapa yang belum selesai.</p>
    </div>
</div>

<!-- ============ LEADERBOARD ALPINE (PAGINATION + EXPORT) ============ -->
<div x-data="leaderboard()" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-10">
    <div class="p-5 md:p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center gap-3 md:justify-between">
        <div>
            <h3 class="text-sm font-extrabold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                Leaderboard Peserta 
                <span class="bg-indigo-100 text-indigo-700 text-[10px] px-2 py-0.5 rounded-full" x-text="filtered.length + ' Peserta'"></span>
            </h3>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
    <div class="relative flex-1">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
        <!-- Pencarian Instan Alpine -->
        <input type="text" x-model="search" @input="page = 1" placeholder="Cari nama / instansi..." class="pl-9 pr-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium focus:outline-none focus:border-[#1ba1e2] w-full sm:w-56">
    </div>
    
    <!-- Tombol Unduh Excel -->
    <button type="button" @click="exportExcel()" class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2.5 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 whitespace-nowrap transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3M4 17v2a2 2 0 002 2h12a2 2 0 002-2v-2M4 7l8-4 8 4M4 7l8 4m-8-4v10m16-10l-8 4m8-4v10m-8-6v10"/></svg>
        Excel
    </button>

    <!-- Tombol Unduh PDF -->
    <button type="button" @click="exportPDF()" class="bg-rose-500 hover:bg-rose-600 text-white px-4 py-2.5 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 whitespace-nowrap transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m-9 8h14a2 2 0 002-2v-5a2 2 0 00-2-2H5a2 2 0 00-2 2v5a2 2 0 002 2z"></path></svg>
        PDF
    </button>
</div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-slate-50 text-[#1a365d] text-xs uppercase tracking-wider border-b border-gray-100">
                    <th class="p-4 font-extrabold text-center">Rank</th>
                    <th class="p-4 font-extrabold">Nama Peserta</th>
                    <th class="p-4 font-extrabold">Instansi</th>
                    <th class="p-4 font-extrabold text-center w-32">Indikator Terisi</th>
                    <th class="p-4 font-extrabold w-48">Rata-rata Nilai</th>
                    <th class="p-4 font-extrabold text-center">Status</th>
                    <th class="p-4 font-extrabold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
                <template x-for="p in paginated" :key="p.id">
                    <!-- Styling Khusus Rank 1, 2, 3 -->
                    <tr :class="{
                        'bg-yellow-50/70 border-b-2 border-yellow-200 hover:bg-yellow-100/70': p.rank === 1,
                        'bg-slate-100/80 border-b-2 border-slate-300 hover:bg-slate-200/70': p.rank === 2,
                        'bg-orange-50/60 border-b-2 border-orange-200 hover:bg-orange-100/60': p.rank === 3,
                        'border-b border-gray-50 hover:bg-blue-50/40': p.rank > 3
                    }">
                        <!-- Icon Rank -->
                        <td class="p-4 text-center cursor-pointer" @click="showDetail(p)">
                            <template x-if="p.rank === 1"><span class="bg-yellow-400 text-yellow-900 px-2 py-1 rounded-md text-xs font-black shadow-sm inline-block w-12">🥇 1</span></template>
                            <template x-if="p.rank === 2"><span class="bg-slate-300 text-slate-800 px-2 py-1 rounded-md text-xs font-black shadow-sm inline-block w-12">🥈 2</span></template>
                            <template x-if="p.rank === 3"><span class="bg-orange-300 text-orange-900 px-2 py-1 rounded-md text-xs font-black shadow-sm inline-block w-12">🥉 3</span></template>
                            <template x-if="p.rank > 3"><span x-text="p.rank" class="text-slate-400 font-extrabold"></span></template>
                        </td>
                        
                        <td class="p-4 font-extrabold text-slate-800 cursor-pointer" @click="showDetail(p)" x-text="p.nama"></td>
                        <td class="p-4 cursor-pointer text-xs" @click="showDetail(p)" x-text="p.instansi"></td>
                        
                        <!-- Progress Indikator -->
                        <td class="p-4 text-center cursor-pointer" @click="showDetail(p)">
                            <div class="w-full bg-slate-200/70 rounded-full h-1.5 overflow-hidden mb-1">
                                <div class="bg-indigo-500 h-full rounded-full" :style="`width: ${p.progress}%`"></div>
                            </div>
                            <span class="text-[10px] font-bold text-slate-500" x-text="p.total_terisi + '/' + p.total_skill"></span>
                        </td>
                        
                        <!-- Skor & Mini Chart -->
                        <td class="p-4 cursor-pointer" @click="showDetail(p)">
                            <div class="flex items-center gap-3">
                                <span class="font-black text-lg w-10 text-right" 
                                      :class="p.lulus_semua ? 'text-emerald-600' : (p.rata_rata_keseluruhan ? 'text-indigo-600' : 'text-slate-300')" 
                                      x-text="p.rata_rata_keseluruhan || '-'"></span>
                                <template x-if="p.rata_rata_keseluruhan">
                                    <div class="flex-1 h-2 bg-slate-200/70 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full transition-all" 
                                             :class="p.lulus_semua ? 'bg-gradient-to-r from-emerald-400 to-emerald-500' : 'bg-gradient-to-r from-indigo-400 to-indigo-500'" 
                                             :style="`width: ${p.rata_rata_keseluruhan}%`"></div>
                                    </div>
                                </template>
                            </div>
                        </td>
                        
                        <!-- Label Status -->
                        <td class="p-4 text-center cursor-pointer" @click="showDetail(p)">
                            <span class="text-[10px] font-bold px-2.5 py-1 rounded-full border"
                                  :class="p.lulus_semua === null ? 'bg-amber-100 text-amber-700 border-amber-200' : (p.lulus_semua ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : 'bg-red-100 text-red-700 border-red-200')"
                                  x-text="p.lulus_semua === null ? 'Belum Lengkap' : (p.lulus_semua ? 'Lulus' : 'Tidak Lulus')"></span>
                        </td>
                        
                        <!-- Aksi -->
                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <!-- Tombol Sertifikat (Gaya Pill Biru) -->
                             <!-- Tombol Sertifikat (Gaya Pill Biru) -->
<a :href="`/sertifikat/{{ $event->uuid }}/${p.uuid}`" 
   target="_blank" 
   class="inline-flex items-center gap-1 bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-all shadow-sm">
    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
    Sertifikat
</a>

                                <!-- Tombol Reset (Gaya Pill Merah Halus) -->
                                <form :id="'reset-' + p.id" :action="`/event/{{ $event->id }}/penilaian-skill/${p.id}/reset`" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="button" 
                                            @click="konfirmasiReset(p.id, p.nama)" 
                                            class="inline-flex items-center gap-1 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-all shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                        Reset
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                </template>
                <tr x-show="paginated.length === 0">
                    <td colspan="7" class="p-10 text-center text-gray-500 font-bold">Data tidak ditemukan.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Paginasi -->
    <div class="p-4 border-t border-gray-100 flex items-center justify-between bg-slate-50/50" x-show="totalPages > 1">
        <p class="text-xs text-slate-500">Halaman <span class="font-bold text-slate-800" x-text="page"></span> dari <span class="font-bold text-slate-800" x-text="totalPages"></span></p>
        <div class="flex gap-1">
            <button @click="prevPage()" :disabled="page === 1" class="px-3 py-1.5 rounded-lg text-xs font-bold border transition-colors" :class="page === 1 ? 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed' : 'bg-white text-slate-700 border-gray-300 hover:bg-gray-50'">Sebelummya</button>
            <button @click="nextPage()" :disabled="page === totalPages" class="px-3 py-1.5 rounded-lg text-xs font-bold border transition-colors" :class="page === totalPages ? 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed' : 'bg-white text-slate-700 border-gray-300 hover:bg-gray-50'">Selanjutnya</button>
        </div>
    </div>
</div>

<script>
// --- Script Pantau Fasilitator (Popup SweetAlert) ---
const progresFasilitatorData = @json($progressFasilitator);
function showProgressFasilitator() {
    let html = progresFasilitatorData.map(f => `
        <div class="mb-4 text-left border-b border-gray-100 pb-3 last:border-0 last:pb-0">
            <div class="flex justify-between items-start mb-1">
                <div>
                    <p class="text-sm font-extrabold text-slate-800">${f.materi}</p>
                    <p class="text-[11px] text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md inline-block mt-1">Fasilitator: <span class="font-bold text-[#1a365d]">${f.fasilitator || '-'}</span></p>
                </div>
                <span class="text-xl font-black ${f.persen === 100 ? 'text-emerald-500' : 'text-indigo-600'}">${f.persen}%</span>
            </div>
            <div class="flex justify-between text-[10px] text-slate-400 mt-2 mb-1">
                <span>Diselesaikan: <b>${f.selesai} peserta</b></span>
                <span>Total: <b>${f.total} peserta</b></span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-1.5">
                <div class="${f.persen === 100 ? 'bg-emerald-500' : 'bg-indigo-500'} h-1.5 rounded-full" style="width: ${f.persen}%"></div>
            </div>
        </div>
    `).join('');

    Swal.fire({
        title: 'Status Penilaian Fasilitator',
        html: `<div class="mt-4 max-h-[60vh] overflow-y-auto pr-2">${html || '<p class="text-sm text-gray-400">Belum ada data materi.</p>'}</div>`,
        confirmButtonText: 'Tutup',
        confirmButtonColor: '#1a365d',
        width: 480,
        customClass: { popup: 'rounded-3xl' }
    });
}

// --- ALPINE JS Component Logic ---
document.addEventListener('alpine:init', () => {
    Alpine.data('leaderboard', () => ({
        search: '',
        page: 1,
        perPage: 10,
        rawPesertas: @json($pesertas), // Tarik semua data dari backend
        
        init() {
            // Pasang rank absolut di awal agar konsisten meski disearch
            this.rawPesertas = this.rawPesertas.map((p, index) => {
                p.rank = index + 1;
                return p;
            });
        },
        get filtered() {
            if (this.search.trim() === '') return this.rawPesertas;
            const q = this.search.toLowerCase();
            return this.rawPesertas.filter(p => (p.nama + ' ' + p.instansi).toLowerCase().includes(q));
        },
        get paginated() {
            const start = (this.page - 1) * this.perPage;
            return this.filtered.slice(start, start + this.perPage);
        },
        get totalPages() {
            return Math.max(1, Math.ceil(this.filtered.length / this.perPage));
        },
        nextPage() { if (this.page < this.totalPages) this.page++; },
        prevPage() { if (this.page > 1) this.page--; },
        
        showDetail(d) {
            let rows = (d.breakdown_materi || []).map(m => `
                <tr class="border-b border-gray-100">
                    <td class="py-2 pr-3 text-left text-gray-700 font-medium">${m.nama_materi}</td>
                    <td class="py-2 text-center text-gray-500">${m.jumlah_dinilai}/${m.jumlah_skill}</td>
                    <td class="py-2 text-center font-bold text-indigo-600">${m.rata_rata ?? '-'}</td>
                    <td class="py-2 text-center text-[10px]">
                        ${m.lulus === null ? '<span class="bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full border border-amber-200">Belum</span>' : (m.lulus ? '<span class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full border border-emerald-200">Lulus</span>' : '<span class="bg-red-100 text-red-600 px-2 py-0.5 rounded-full border border-red-200">Gagal</span>')}
                    </td>
                </tr>
            `).join('');

            Swal.fire({
                title: d.nama,
                html: `
                    <div class="text-left mt-2">
                        <p class="text-xs text-gray-500 mb-4 bg-gray-50 p-2 rounded-lg border border-gray-100"><span class="font-bold">Instansi:</span> ${d.instansi}<br><span class="font-bold">Progres Keseluruhan:</span> ${d.total_terisi}/${d.total_skill} indikator terjawab.</p>
                        <table class="w-full text-sm mb-2">
                            <thead><tr class="text-[10px] text-gray-400 uppercase border-b border-gray-200"><th class="text-left pb-2">Materi</th><th class="pb-2">Terisi</th><th class="pb-2">Nilai</th><th class="pb-2">Status</th></tr></thead>
                            <tbody>${rows || '<tr><td colspan="4" class="py-3 text-center text-gray-400 text-xs">Belum ada penilaian.</td></tr>'}</tbody>
                        </table>
                    </div>
                `,
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#1a365d',
                customClass: { popup: 'rounded-3xl' },
                width: 520,
            });
        },
        
        konfirmasiReset(id, nama) {
            Swal.fire({
                title: 'Reset Penilaian?',
                html: `Nilai skill atas nama <b>${nama}</b> akan dihapus bersih.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Reset',
                confirmButtonColor: '#ef4444',
                cancelButtonText: 'Batal',
                customClass: { popup: 'rounded-2xl' }
            }).then((result) => {
                if (result.isConfirmed) document.getElementById('reset-' + id).submit();
            });
        },
        
     exportPDF() {
            if (typeof window.jspdf === 'undefined') {
                Swal.fire('Error!', 'Library jsPDF belum termuat. Pastikan CDN sudah ditambahkan.', 'error');
                return;
            }

            // Tampilkan loading spinner karena kita perlu waktu (milidetik) untuk memuat gambar logo
            Swal.fire({
                title: 'Menyiapkan Dokumen...',
                text: 'Membentuk PDF beserta KOP resmi...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('landscape'); 
            const baseUrl = window.location.origin;

            // Fungsi utama render PDF (akan dipanggil setelah logo berhasil diload)
            const renderPDF = (logoImg = null) => {
                // ==========================================
                // 1. BAGIAN KOP SURAT
                // ==========================================
                if (logoImg) {
                    // (gambar, format, x, y, lebar, tinggi)
                    doc.addImage(logoImg, 'PNG', 14, 10, 22, 22);
                }

                // Geser teks ke kanan jika ada logo (agar tidak nabrak gambar)
                const textStartX = logoImg ? 40 : 14;

                // Baris 1: Nama Lembaga
                doc.setFontSize(16);
                doc.setFont("helvetica", "bold");
                doc.setTextColor(26, 54, 93); // Warna biru gelap (Tailwind slate-900 / indigo)
                doc.text("FLASH INSPIRE TRAINING CENTER", textStartX, 17);

                // Baris 2: Akreditasi
                doc.setFontSize(11);
                doc.setFont("helvetica", "normal");
                doc.setTextColor(50, 50, 50); // Abu-abu gelap
                doc.text("Lembaga Pelatihan Terakreditasi Madya (B) Kemenkes RI", textStartX, 23);
                
                // Baris 3: SK
                doc.setFontSize(10);
                doc.text("SK. Dirjen SDM Kesehatan No. HK 02.02/F/3885/2025", textStartX, 28);

                // Garis Pembatas KOP
                doc.setLineWidth(0.8);
                doc.setDrawColor(26, 54, 93);
                // line(x1, y1, x2, y2) -> kertas landscape A4 lebarnya 297, margin kanan 14 = 283
                doc.line(14, 34, 283, 34); 

                // ==========================================
                // 2. JUDUL DOKUMEN
                // ==========================================
                doc.setFontSize(12);
                doc.setFont("helvetica", "bold");
                doc.setTextColor(0, 0, 0);
                doc.text("Penilaian {{ $event->nama_event }}", 14, 43);

                doc.setFontSize(9);
                doc.setFont("helvetica", "normal");
                doc.setTextColor(100, 100, 100);
                doc.text("Diekspor pada: " + new Date().toLocaleDateString('id-ID'), 14, 48);

                // ==========================================
                // 3. TABEL DENGAN CLICKABLE LINK
                // ==========================================
                const tableColumn = ["No", "Nama", "Instansi", "Progres", "Skor", "Sertifikat"];
                const tableRows = [];

                this.filtered.forEach(p => {
                    tableRows.push([
                        p.rank,
                        p.nama,
                        p.instansi,
                        `${p.total_terisi}/${p.total_skill}`,
                        p.rata_rata_keseluruhan || 0,
                        "Buka Sertifikat" // Mengganti URL panjang dengan teks cantik
                    ]);
                });

                doc.autoTable({
                    head: [tableColumn],
                    body: tableRows,
                    startY: 53, // Mulai dari y=53 (di bawah KOP dan Judul)
                    styles: { fontSize: 9, cellPadding: 3, valign: 'middle' },
                    headStyles: { fillColor: [26, 54, 93], textColor: 255 }, // Header biru tua
                    columnStyles: {
                        0: { halign: 'center', cellWidth: 15 },
                        3: { halign: 'center', cellWidth: 25 },
                        4: { halign: 'center', fontStyle: 'bold', cellWidth: 20 },
                        // Style untuk kolom link agar warnanya biru dan tebal seperti hyperlink
                        5: { textColor: [2, 132, 199], fontStyle: 'bold', halign: 'center', cellWidth: 35 } 
                    },
                    alternateRowStyles: { fillColor: [249, 250, 251] },
                    
                    // Hook untuk menggambar elemen ekstra (link interaktif)
                    didDrawCell: (data) => {
                        // Jika sel yang sedang digambar adalah kolom index 5 (Sertifikat) di isi tabel (body)
                        if (data.column.index === 5 && data.cell.section === 'body') {
                            const p = this.filtered[data.row.index];
                            const linkSertifikat = `${baseUrl}/sertifikat/{{ $event->uuid }}/${p.uuid}`;
                            
                            // Tambahkan interaksi klik tak terlihat di atas kotak teks
                            doc.link(data.cell.x, data.cell.y, data.cell.width, data.cell.height, { url: linkSertifikat });
                        }
                    }
                });

                // Simpan & Tutup Loading
                doc.save(`Leaderboard_{{ Str::slug($event->nama_event) }}.pdf`);
                Swal.close();
            };

            // ==========================================
            // PRE-LOAD GAMBAR (LOGO)
            // ==========================================
            const logoUrl = '/storage/ikon.png'; // Pastikan path ini benar mengarah ke logo kamu
            const img = new Image();
            img.src = logoUrl;
            
            // Jika logo berhasil diload, jalankan fungsi pembuat PDF dengan logo
            img.onload = () => renderPDF(img);
            
            // Fallback: Jika gambar gagal diload (misal path salah/tidak ada), cetak tanpa gambar tanpa bikin error
            img.onerror = () => {
                console.warn("Logo KOP tidak ditemukan di path: " + logoUrl);
                renderPDF(null);
            };
        }
    }));
});
</script>
@endsection