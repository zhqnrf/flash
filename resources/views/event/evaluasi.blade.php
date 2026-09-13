@extends('layouts.admin')
@section('title', 'Evaluasi Pelatihan - ' . $event->nama_event)

@section('content')

@if(session('success'))
<script>document.addEventListener('DOMContentLoaded', () => Swal.fire({icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", timer: 2200, showConfirmButton: false, customClass: {popup: 'rounded-2xl'}}));</script>
@endif

<div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-end gap-4">
    <div>
        <a href="{{ route('event.index') }}" class="text-xs font-bold text-slate-400 hover:text-[#1a365d] flex items-center gap-1 mb-2">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Daftar Event
        </a>
        <h2 class="text-3xl font-extrabold text-[#1a365d]">Dashboard Evaluasi Pelatihan</h2>
        <p class="text-gray-500 text-sm mt-1">{{ $event->nama_event }} — {{ optional($event->pelatihan)->nama_pelatihan }}</p>
    </div>
</div>

<!-- ============ STATISTIK ============ -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Peserta Diterima</p>
        <p class="text-2xl font-extrabold text-slate-800 mt-1">{{ $totalDiterima }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Sudah Mengisi</p>
        <p class="text-2xl font-extrabold text-emerald-600 mt-1">{{ $totalSudahIsi }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Belum Mengisi</p>
        <p class="text-2xl font-extrabold text-amber-500 mt-1">{{ $totalBelumIsi }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Rata-rata Skor</p>
        <p class="text-2xl font-extrabold text-indigo-600 mt-1">{{ $rataRataKeseluruhan }}</p>
    </div>
</div>

<!-- ============ RATA-RATA PER KRITERIA ============ -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 md:p-6 mb-6">
    <h3 class="text-sm font-extrabold text-slate-700 uppercase tracking-wider mb-4">Rata-rata Nilai per Kriteria Pelatihan</h3>
    <div class="space-y-4">
        @forelse($rataPerKriteria as $k)
        <div>
            <div class="flex justify-between text-xs font-bold text-slate-600 mb-1.5">
                <span>{{ $k['nama'] }}</span>
                <span class="text-indigo-600">{{ $k['rata_rata'] }} / {{ $k['maksimal'] }}</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-400 to-indigo-600 h-2.5 rounded-full" style="width: {{ min(100, round(($k['rata_rata'] / max($k['maksimal'], 1)) * 100)) }}%"></div>
            </div>
        </div>
        @empty
        <p class="text-xs text-slate-400">Belum ada data penilaian masuk.</p>
        @endforelse
    </div>
</div>

<!-- ============ DAFTAR SARAN PESERTA ============ -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6" x-data="{ searchSaran: '' }">
    <div class="p-5 md:p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center gap-3 md:justify-between">
        <h3 class="text-sm font-extrabold text-slate-700 uppercase tracking-wider">Saran &amp; Masukan Peserta ({{ $daftarSaran->count() }})</h3>
        <div class="relative w-full md:w-64">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <input type="text" x-model="searchSaran" placeholder="Cari nama / isi saran..." class="pl-9 pr-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium focus:outline-none focus:border-[#1ba1e2] w-full">
        </div>
    </div>
    <div class="divide-y divide-gray-50 max-h-96 overflow-y-auto">
        @forelse($daftarSaran as $s)
        <div data-search="{{ strtolower($s['nama'].' '.$s['instansi'].' '.$s['saran']) }}"
             x-show="$el.dataset.search.includes(searchSaran.toLowerCase())"
             class="p-4">
            <div class="flex justify-between items-start mb-1 gap-3">
                <p class="text-sm font-extrabold text-slate-800">{{ $s['nama'] }} <span class="text-xs font-normal text-slate-400">— {{ $s['instansi'] }}</span></p>
                <span class="text-[10px] text-slate-400 whitespace-nowrap">{{ $s['tanggal'] }}</span>
            </div>
            <p class="text-sm text-slate-600">{{ $s['saran'] }}</p>
        </div>
        @empty
        <p class="p-6 text-center text-xs text-slate-400 font-bold">Belum ada saran dari peserta.</p>
        @endforelse
    </div>
</div>

<!-- ============ TABEL PESERTA (Search + Filter + Export + Klik Detail + Hapus) ============ -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6" x-data="{ search: '', filterStatus: 'semua' }">
    <div class="p-5 md:p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center gap-3 md:justify-between">
        <div>
            <h3 class="text-sm font-extrabold text-slate-700 uppercase tracking-wider">Status Pengisian per Peserta</h3>
            <p class="text-[11px] text-slate-400 mt-0.5">Klik baris untuk lihat detail. Tombol hapus buat reset biar peserta bisa isi ulang.</p>
        </div>

        <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" x-model="search" placeholder="Cari nama / instansi..." class="pl-9 pr-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium focus:outline-none focus:border-[#1ba1e2] w-full sm:w-56">
            </div>
            <select x-model="filterStatus" class="px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-bold focus:outline-none">
                <option value="semua">Semua Status</option>
                <option value="sudah">Sudah Mengisi</option>
                <option value="belum">Belum Mengisi</option>
            </select>
            <button type="button" onclick="exportPesertaExcel()" class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-1.5 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3M4 17v2a2 2 0 002 2h12a2 2 0 002-2v-2M4 7l8-4 8 4M4 7l8 4m-8-4v10m16-10l-8 4m8-4v10m-8-6v10"/></svg>
                Unduh Excel
            </button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table id="tabel-peserta" class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-slate-50 text-[#1a365d] text-xs uppercase tracking-wider border-b border-gray-100">
                    <th class="p-4 font-extrabold">Nama Peserta</th>
                    <th class="p-4 font-extrabold">Instansi</th>
                    <th class="p-4 font-extrabold text-center">Status</th>
                    <th class="p-4 font-extrabold text-center">Rata-rata Nilai</th>
                    <th class="p-4 font-extrabold">Tanggal Isi</th>
                    <th class="p-4 font-extrabold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
                @forelse($pesertas as $p)
                @php
                    $detailPeserta = [
                        'nama' => $p->nama_lengkap,
                        'instansi' => $p->instansi,
                        'status' => $p->sudah_isi ? 'Sudah Mengisi' : 'Belum Mengisi',
                        'saran' => $p->saran,
                        'jawaban' => $p->detail_jawaban,
                    ];
                @endphp
                <tr data-row
                    data-search="{{ strtolower($p->nama_lengkap.' '.$p->instansi) }}"
                    data-nama="{{ $p->nama_lengkap }}"
                    data-instansi="{{ $p->instansi }}"
                    data-status="{{ $p->sudah_isi ? 'sudah' : 'belum' }}"
                    data-rata="{{ $p->rata_rata ?? '-' }}"
                    data-tanggal="{{ $p->tanggal_isi ? $p->tanggal_isi->format('d M Y H:i') : '-' }}"
                    data-detail='@json($detailPeserta, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG)'
                    x-show="(filterStatus === 'semua' || filterStatus === '{{ $p->sudah_isi ? 'sudah' : 'belum' }}') && $el.dataset.search.includes(search.toLowerCase())"
                    class="hover:bg-blue-50/40 border-b border-gray-50">
                    <td class="p-4 font-extrabold text-slate-800 cursor-pointer" onclick="showDetailPeserta(this.closest('tr'))">{{ $p->nama_lengkap }}</td>
                    <td class="p-4 cursor-pointer" onclick="showDetailPeserta(this.closest('tr'))">{{ $p->instansi }}</td>
                    <td class="p-4 text-center cursor-pointer" onclick="showDetailPeserta(this.closest('tr'))">
                        @if($p->sudah_isi)
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full border bg-emerald-100 text-emerald-700 border-emerald-200">Sudah Mengisi</span>
                        @else
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full border bg-amber-100 text-amber-700 border-amber-200">Belum Mengisi</span>
                        @endif
                    </td>
                    <td class="p-4 text-center font-extrabold text-indigo-600 cursor-pointer" onclick="showDetailPeserta(this.closest('tr'))">{{ $p->rata_rata ?? '-' }}</td>
                    <td class="p-4 text-xs text-gray-500 cursor-pointer" onclick="showDetailPeserta(this.closest('tr'))">{{ $p->tanggal_isi ? $p->tanggal_isi->format('d M Y H:i') : '-' }}</td>
                    <td class="p-4 text-center">
                        @if($p->sudah_isi)
                        <form id="hapus-evaluasi-{{ $p->id }}" action="{{ route('evaluasi-peserta.hapus', $p->id) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="button" onclick="konfirmasiHapusEvaluasi('{{ $p->id }}', '{{ addslashes($p->nama) }}')" class="text-xs font-bold text-red-500 hover:text-red-700 hover:underline">Hapus</button>
                        </form>
                        @else
                        <span class="text-xs text-gray-300">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="p-10 text-center text-gray-500 font-bold">Belum ada peserta diterima pada event ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- ============ REKAP PER FASILITATOR: 2 KOMPONEN (Search + Export + Klik Detail) ============ -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden" x-data="{ searchFasil: '' }">
    <div class="p-5 md:p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center gap-3 md:justify-between">
        <div>
            <h3 class="text-sm font-extrabold text-slate-700 uppercase tracking-wider">Rekap Nilai per Fasilitator</h3>
            <p class="text-[11px] text-slate-400 mt-0.5">Komponen Materi (per materi) dan Komponen Fasilitator (1x keseluruhan) ditampilkan terpisah. Klik baris untuk detail.</p>
        </div>
        <div class="flex gap-2 w-full md:w-auto">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" x-model="searchFasil" placeholder="Cari nama fasilitator..." class="pl-9 pr-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium focus:outline-none focus:border-[#1ba1e2] w-full sm:w-56">
            </div>
            <button type="button" onclick="exportFasilitatorExcel()" class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-1.5 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3M4 17v2a2 2 0 002 2h12a2 2 0 002-2v-2M4 7l8-4 8 4M4 7l8 4m-8-4v10m16-10l-8 4m8-4v10m-8-6v10"/></svg>
                Unduh Excel
            </button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table id="tabel-fasilitator" class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-slate-50 text-[#1a365d] text-xs uppercase tracking-wider border-b border-gray-100">
                    <th class="p-4 font-extrabold">Nama Fasilitator</th>
                    <th class="p-4 font-extrabold">Materi Diajarkan</th>
                    <th class="p-4 font-extrabold text-center">Rata² Komponen Materi</th>
                    <th class="p-4 font-extrabold text-center">Rata² Komponen Fasilitator</th>
                    <th class="p-4 font-extrabold text-center">Jml Menilai Fasilitator</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
                @forelse($rekapFasilitator as $f)
                @php
                    $materiText = $f['materi']->implode(', ');
                    $detailFasil = [
                        'nama' => $f['nama'],
                        'materi' => $f['materi'],
                        'rata_rata_materi' => $f['rata_rata_materi'],
                        'rata_rata_fasilitator' => $f['rata_rata_fasilitator'],
                        'jumlah_menilai_fasilitator' => $f['jumlah_menilai_fasilitator'],
                        'materi_breakdown' => $f['materi_breakdown'],
                        'peserta_detail_materi' => $f['peserta_detail_materi'],
                        'peserta_detail_fasilitator' => $f['peserta_detail_fasilitator'],
                    ];
                @endphp
                <tr data-row-fasil
                    data-search="{{ strtolower($f['nama']) }}"
                    data-nama="{{ $f['nama'] }}"
                    data-materi="{{ $materiText }}"
                    data-rata-materi="{{ $f['rata_rata_materi'] ?? '-' }}"
                    data-rata-fasil="{{ $f['rata_rata_fasilitator'] ?? '-' }}"
                    data-jumlah-fasil="{{ $f['jumlah_menilai_fasilitator'] }}"
                    data-detail='@json($detailFasil, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG)'
                    onclick="showDetailFasilitator(this)"
                    x-show="$el.dataset.search.includes(searchFasil.toLowerCase())"
                    class="hover:bg-blue-50/40 border-b border-gray-50 cursor-pointer">
                    <td class="p-4 font-extrabold text-slate-800">{{ $f['nama'] }}</td>
                    <td class="p-4 text-xs text-gray-500">{{ $materiText ?: '-' }}</td>
                    <td class="p-4 text-center font-extrabold text-indigo-600">{{ $f['rata_rata_materi'] ?? '-' }}</td>
                    <td class="p-4 text-center font-extrabold text-indigo-600">{{ $f['rata_rata_fasilitator'] ?? '-' }}</td>
                    <td class="p-4 text-center font-bold">{{ $f['jumlah_menilai_fasilitator'] }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="p-10 text-center text-gray-500 font-bold">Belum ada fasilitator yang ditugaskan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
function escapeHtml(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.innerText = str;
    return div.innerHTML;
}

function showDetailPeserta(el) {
    const d = JSON.parse(el.dataset.detail);
    let rows = (d.jawaban || []).map(j => `
        <tr class="border-b border-gray-100">
            <td class="py-2 pr-3 text-left text-gray-600">${escapeHtml(j.kriteria)}</td>
            <td class="py-2 text-center font-bold text-indigo-600">${j.nilai}</td>
        </tr>
    `).join('');

    Swal.fire({
        title: escapeHtml(d.nama),
        html: `
            <div class="text-left">
                <p class="text-xs text-gray-500 mb-3">${escapeHtml(d.instansi)} &middot; ${d.status}</p>
                ${d.jawaban && d.jawaban.length ? `
                    <table class="w-full text-sm mb-4">
                        <thead><tr class="text-xs text-gray-400 uppercase"><th class="text-left pb-2">Kriteria</th><th class="pb-2">Nilai</th></tr></thead>
                        <tbody>${rows}</tbody>
                    </table>
                ` : '<p class="text-xs text-gray-400 mb-4">Belum mengisi evaluasi pelatihan.</p>'}
                ${d.saran ? `<div class="bg-blue-50 border border-blue-100 rounded-xl p-3"><p class="text-xs font-bold text-blue-700 mb-1">Saran / Masukan:</p><p class="text-sm text-gray-700">${escapeHtml(d.saran)}</p></div>` : ''}
            </div>
        `,
        confirmButtonText: 'Tutup',
        confirmButtonColor: '#1a365d',
        customClass: { popup: 'rounded-3xl' },
        width: 480,
    });
}

function konfirmasiHapusEvaluasi(id, nama) {
    Swal.fire({
        title: 'Hapus Evaluasi Peserta?',
        html: `Semua jawaban evaluasi pelatihan atas nama <b>${escapeHtml(nama)}</b> akan dihapus dan peserta bisa mengisi ulang dari awal. Lanjutkan?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        confirmButtonColor: '#ef4444',
        cancelButtonText: 'Batal',
        customClass: { popup: 'rounded-2xl' }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('hapus-evaluasi-' + id).submit();
        }
    });
}

function showDetailFasilitator(el) {
    const d = JSON.parse(el.dataset.detail);

    let materiBreakdownHtml = (d.materi_breakdown || []).map(m => `
        <div class="bg-slate-50 rounded-xl p-2.5 border border-slate-100">
            <p class="text-[10px] font-bold text-slate-400 uppercase truncate">${escapeHtml(m.nama_materi)}</p>
            <p class="text-sm font-extrabold text-indigo-600">${m.rata_rata ?? '-'} <span class="text-[10px] font-normal text-slate-400">(${m.jumlah_menilai} nilai)</span></p>
        </div>
    `).join('');

    let pesertaMateriRows = (d.peserta_detail_materi || []).map(p => {
        let list = (p.jawaban || []).map(j => `<div class="flex justify-between"><span>${escapeHtml(j.materi)}</span><b class="text-indigo-600 ml-2">${j.nilai}</b></div>`).join('');
        return `
            <div class="border border-gray-100 rounded-xl p-3 mb-2">
                <p class="text-sm font-bold text-gray-800">${escapeHtml(p.nama)}</p>
                <p class="text-xs text-gray-400 mb-2">${escapeHtml(p.instansi)}</p>
                <div class="text-xs text-gray-500 space-y-0.5">${list}</div>
                ${p.saran ? `<p class="text-xs text-blue-700 bg-blue-50 rounded-lg p-2 mt-2">💬 ${escapeHtml(p.saran)}</p>` : ''}
            </div>
        `;
    }).join('') || '<p class="text-xs text-gray-400">Belum ada penilaian materi.</p>';

    let pesertaFasilRows = (d.peserta_detail_fasilitator || []).map(p => {
        let list = (p.jawaban || []).map(j => `<div class="flex justify-between"><span>${escapeHtml(j.kriteria)}</span><b class="text-indigo-600 ml-2">${j.nilai}</b></div>`).join('');
        return `
            <div class="border border-gray-100 rounded-xl p-3 mb-2">
                <p class="text-sm font-bold text-gray-800">${escapeHtml(p.nama)} <span class="text-indigo-600 font-extrabold">(${p.rata_rata})</span></p>
                <p class="text-xs text-gray-400 mb-2">${escapeHtml(p.instansi)}</p>
                <div class="text-xs text-gray-500 space-y-0.5">${list}</div>
                ${p.saran ? `<p class="text-xs text-blue-700 bg-blue-50 rounded-lg p-2 mt-2">💬 ${escapeHtml(p.saran)}</p>` : ''}
            </div>
        `;
    }).join('') || '<p class="text-xs text-gray-400">Belum ada penilaian fasilitator.</p>';

    Swal.fire({
        title: escapeHtml(d.nama),
        html: `
            <div class="text-left max-h-[65vh] overflow-y-auto pr-2">
                <p class="text-xs text-gray-500 mb-3">Materi: ${escapeHtml((d.materi || []).join(', ')) || '-'}</p>

                <p class="text-xs font-extrabold text-slate-500 uppercase tracking-wide mb-2">Komponen Materi</p>
                <div class="grid grid-cols-2 gap-2 mb-4">${materiBreakdownHtml}</div>
                ${pesertaMateriRows}

                <p class="text-xs font-extrabold text-slate-500 uppercase tracking-wide mb-2 mt-4">Komponen Fasilitator (Rata-rata: ${d.rata_rata_fasilitator ?? '-'}, ${d.jumlah_menilai_fasilitator} penilai)</p>
                ${pesertaFasilRows}
            </div>
        `,
        confirmButtonText: 'Tutup',
        confirmButtonColor: '#1a365d',
        customClass: { popup: 'rounded-3xl' },
        width: 560,
    });
}

function exportPesertaExcel() {
    const rows = document.querySelectorAll('#tabel-peserta tbody tr[data-row]');
    const data = [['Nama Peserta', 'Instansi', 'Status', 'Rata-rata Nilai', 'Tanggal Isi']];
    rows.forEach(row => {
        if (row.style.display !== 'none') {
            data.push([row.dataset.nama, row.dataset.instansi, row.dataset.status === 'sudah' ? 'Sudah Mengisi' : 'Belum Mengisi', row.dataset.rata, row.dataset.tanggal]);
        }
    });
    const ws = XLSX.utils.aoa_to_sheet(data);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Rekap Peserta');
    XLSX.writeFile(wb, `Rekap_Evaluasi_Pelatihan_{{ Str::slug($event->nama_event) }}.xlsx`);
}

function exportFasilitatorExcel() {
    const rows = document.querySelectorAll('#tabel-fasilitator tbody tr[data-row-fasil]');
    const data = [['Nama Fasilitator', 'Materi Diajarkan', 'Rata-rata Komponen Materi', 'Rata-rata Komponen Fasilitator', 'Jumlah Menilai Fasilitator']];
    rows.forEach(row => {
        if (row.style.display !== 'none') {
            data.push([row.dataset.nama, row.dataset.materi, row.dataset.rataMateri, row.dataset.rataFasil, row.dataset.jumlahFasil]);
        }
    });
    const ws = XLSX.utils.aoa_to_sheet(data);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Rekap Fasilitator');
    XLSX.writeFile(wb, `Rekap_Evaluasi_Fasilitator_{{ Str::slug($event->nama_event) }}.xlsx`);
}
</script>
@endsection