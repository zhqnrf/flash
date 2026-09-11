@extends('layouts.admin')
@section('title', 'Rekap Absensi - ' . $event->nama_event)

@section('content')

<!-- Include SheetJS (XLSX) CDN -->
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>

<div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-end gap-4">
    <div>
        <a href="{{ route('event.index') }}" class="text-xs font-bold text-slate-400 hover:text-[#1a365d] flex items-center gap-1 mb-2">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Daftar Event
        </a>
        <h2 class="text-3xl font-extrabold text-[#1a365d]">Rekap Absensi Peserta</h2>
        <p class="text-gray-500 text-sm mt-1">Pantau kehadiran peserta yang telah diterima pada event ini.</p>
    </div>
    
    <div class="flex gap-2">
        <!-- Tombol Salin Link Absensi Publik -->
        <button onclick="copyLinkAbsensi('{{ route('presensi.public', $event->uuid) }}')" class="bg-white border border-blue-200 text-blue-700 hover:bg-blue-50 px-4 py-2 rounded-xl text-sm font-bold shadow-sm flex items-center gap-2 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
            Salin Link Presensi
        </button>
    </div>
</div>

<!-- ============ DASHBOARD REKAP ABSENSI ============ -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
  <!-- Status Portal Absensi -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex flex-col justify-between relative overflow-hidden group">
        <div class="flex items-center gap-3 mb-4">
            @if($event->status_absen === 'tutup_paksa')
                <div class="bg-red-100 text-red-600 p-2.5 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Status Portal</p>
                    <p class="text-base font-extrabold text-red-600 leading-tight">Ditutup Paksa</p>
                </div>
            @elseif($event->status_absen === 'buka_paksa')
                <div class="bg-emerald-100 text-emerald-600 p-2.5 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Status Portal</p>
                    <p class="text-base font-extrabold text-emerald-600 leading-tight">Dibuka Paksa</p>
                </div>
            @else
                <div class="bg-blue-100 text-blue-600 p-2.5 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Mode Otomatis</p>
                    @if($bukaAbsensi)
                        <p class="text-base font-extrabold text-emerald-600 leading-tight">Terbuka</p>
                    @else
                        <p class="text-base font-extrabold text-amber-600 leading-tight">Luar Jam</p>
                    @endif
                </div>
            @endif
        </div>
        
        <!-- 3 Tombol Kendali Cepat -->
        <div class="grid grid-cols-3 gap-1.5 mt-auto">
            <form action="{{ route('event.status-absen', $event->id) }}" method="POST">
                @csrf <input type="hidden" name="status_absen" value="buka_paksa">
                <button type="submit" class="w-full text-[9px] font-bold uppercase tracking-wider py-1.5 rounded border transition-colors {{ $event->status_absen === 'buka_paksa' ? 'bg-emerald-500 text-white border-emerald-600' : 'bg-emerald-50 text-emerald-600 border-emerald-200 hover:bg-emerald-500 hover:text-white' }}">Buka</button>
            </form>
            <form action="{{ route('event.status-absen', $event->id) }}" method="POST">
                @csrf <input type="hidden" name="status_absen" value="otomatis">
                <button type="submit" class="w-full text-[9px] font-bold uppercase tracking-wider py-1.5 rounded border transition-colors {{ $event->status_absen === 'otomatis' ? 'bg-blue-500 text-white border-blue-600' : 'bg-blue-50 text-blue-600 border-blue-200 hover:bg-blue-500 hover:text-white' }}">Otomatis</button>
            </form>
            <form action="{{ route('event.status-absen', $event->id) }}" method="POST">
                @csrf <input type="hidden" name="status_absen" value="tutup_paksa">
                <button type="submit" class="w-full text-[9px] font-bold uppercase tracking-wider py-1.5 rounded border transition-colors {{ $event->status_absen === 'tutup_paksa' ? 'bg-red-500 text-white border-red-600' : 'bg-red-50 text-red-600 border-red-200 hover:bg-red-500 hover:text-white' }}">Tutup</button>
            </form>
        </div>
    </div>

    <!-- Total Diterima -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="bg-indigo-100 text-indigo-600 p-3 rounded-xl">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
        </div>
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Total Peserta Valid</p>
            <p class="text-lg font-extrabold text-[#1a365d]">{{ $totalDiterima }} <span class="text-sm font-normal text-gray-400">Orang</span></p>
        </div>
    </div>

    <!-- Total Hadir -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="bg-emerald-100 text-emerald-600 p-3 rounded-xl">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
        </div>
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Sudah Absen</p>
            <p class="text-lg font-extrabold text-emerald-600">{{ $totalHadir }} <span class="text-sm font-normal text-emerald-400">Hadir</span></p>
            <p class="text-[10px] font-bold text-emerald-500 mt-0.5">Persentase: {{ $persenHadir }}%</p>
        </div>
    </div>

    <!-- Total Belum Hadir -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="bg-orange-100 text-orange-600 p-3 rounded-xl">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Belum Absen (Alpa)</p>
            <p class="text-lg font-extrabold text-orange-600">{{ $totalBelumHadir }} <span class="text-sm font-normal text-orange-400">Peserta</span></p>
        </div>
    </div>
</div>

<!-- ============ FILTER & PENCARIAN ============ -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 md:p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-widest">Pencarian & Filter Absensi</h3>
        
        <button type="button" onclick="exportDataKeExcel()" class="bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white px-4 py-2 rounded-xl text-xs font-bold transition-colors flex items-center gap-1.5 border border-emerald-200 hover:border-emerald-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Export Rekap (Excel)
        </button>
    </div>

    <form action="{{ route('event.absensi', $event->id) }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
        <div class="md:col-span-5">
            <label class="block text-xs font-bold text-slate-600 mb-2">Cari Nama atau Instansi</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik kata kunci..." class="pl-10 w-full rounded-xl border-gray-200 text-sm py-2.5 focus:ring-[#1a365d] bg-slate-50 focus:bg-white">
            </div>
        </div>

        <div class="md:col-span-3">
            <label class="block text-xs font-bold text-slate-600 mb-2">Status Kehadiran</label>
            <select name="status" class="w-full rounded-xl border-gray-200 text-sm py-2.5 focus:ring-[#1a365d] bg-slate-50 focus:bg-white cursor-pointer">
                <option value="Semua" {{ request('status') == 'Semua' ? 'selected' : '' }}>Semua Status</option>
                <option value="Hadir" {{ request('status') == 'Hadir' ? 'selected' : '' }}>Sudah Hadir</option>
                <option value="Belum" {{ request('status') == 'Belum' ? 'selected' : '' }}>Belum Hadir</option>
            </select>
        </div>

        <div class="md:col-span-2">
            <label class="block text-xs font-bold text-slate-600 mb-2">Urutkan</label>
            <select name="sort" class="w-full rounded-xl border-gray-200 text-sm py-2.5 focus:ring-[#1a365d] bg-slate-50 focus:bg-white cursor-pointer">
                <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Terbaru (Daftar)</option>
                <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Nama (A-Z)</option>
            </select>
        </div>

        <div class="md:col-span-2">
            <button type="submit" class="w-full bg-[#1a365d] text-white py-2.5 rounded-xl text-sm font-bold shadow-sm hover:bg-[#122643] transition-colors flex items-center justify-center gap-2">
                Terapkan Filter
            </button>
        </div>
    </form>
</div>

<!-- ============ DAFTAR PESERTA ABSENSI ============ -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-slate-50 text-[#1a365d] text-xs uppercase tracking-wider border-b border-gray-100">
                    <th class="p-4 font-extrabold w-12 text-center">No</th>
                    <th class="p-4 font-extrabold">Nama Peserta</th>
                    <th class="p-4 font-extrabold">Instansi & Kontak</th>
                    <th class="p-4 font-extrabold text-center">Status Kehadiran</th>
                    <th class="p-4 font-extrabold">Waktu Presensi</th>
                    <th class="p-4 font-extrabold text-center">Foto Selfie</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
                @forelse($pesertas as $i => $p)
                <tr class="hover:bg-blue-50/40 border-b border-gray-50 align-top transition-colors">
                    <td class="p-4 text-center text-gray-400 font-bold">{{ $pesertas->firstItem() + $i }}</td>
                    
                    <td class="p-4">
                        <div class="font-extrabold text-slate-800">{{ trim($p->gelar_depan . ' ' . $p->nama . ' ' . $p->gelar_belakang) }}</div>
                        <div class="text-[10px] bg-slate-100 px-2 py-0.5 rounded text-gray-500 inline-block mt-1 border border-slate-200">
                            NIK: {{ $p->nik }}
                        </div>
                    </td>
                    
                    <td class="p-4">
                        <div class="font-bold text-slate-700">{{ $p->instansi }}</div>
                        <div class="text-xs text-blue-600 mt-0.5">{{ $p->email_plataran_sehat }}</div>
                    </td>
                    
                    <td class="p-4 text-center">
                        @if($p->absensi)
                            <span class="text-xs font-bold px-3 py-1.5 rounded-full border bg-emerald-100 text-emerald-700 border-emerald-200">Hadir</span>
                        @else
                            <span class="text-xs font-bold px-3 py-1.5 rounded-full border bg-red-50 text-red-600 border-red-200">Belum Hadir</span>
                        @endif
                    </td>

                    <td class="p-4">
                        @if($p->absensi)
                            <div class="font-extrabold text-emerald-700">{{ $p->absensi->jam_masuk->format('H:i') }} WIB</div>
                            <div class="text-[10px] text-gray-400">{{ $p->absensi->jam_masuk->translatedFormat('d M Y') }}</div>
                        @else
                            <div class="text-gray-400 italic text-xs">-</div>
                        @endif
                    </td>

              <td class="p-4 text-center">
        @if($p->absensi && $p->absensi->foto_selfie)
            <div class="flex flex-col items-center gap-1.5">
                <button type="button" onclick="lihatSelfie('{{ asset('storage/'.$p->absensi->foto_selfie) }}', '{{ addslashes($p->nama) }}', '{{ $p->absensi->jam_masuk->format('H:i') }}')" class="bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white border border-blue-200 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors inline-flex items-center justify-center gap-1.5 shadow-sm w-full max-w-[120px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    Lihat Selfie
                </button>
                
                <!-- Tombol Reset Absensi -->
                <form action="{{ route('absensi.reset', $p->absensi->id) }}" method="POST" class="w-full max-w-[120px]">
                    @csrf @method('DELETE')
                    <button type="button" onclick="konfirmasiReset(this, '{{ addslashes($p->nama) }}')" class="w-full text-[10px] font-bold text-red-500 hover:text-red-700 hover:underline">
                        Reset Absen
                    </button>
                </form>
            </div>
        @else
            <span class="text-xs text-gray-400 border border-gray-200 bg-gray-50 px-3 py-1.5 rounded-lg inline-block">Belum Tersedia</span>
        @endif
    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-16 text-center">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <p class="text-gray-500 font-bold text-lg">Tidak ada data ditemukan</p>
                        <p class="text-gray-400 text-sm">Coba sesuaikan kata kunci atau filter pencarian.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mb-10">{{ $pesertas->links() }}</div>


<!-- ============ MODAL LIHAT SELFIE ============ -->
<div id="modalSelfie" class="fixed inset-0 bg-slate-900/80 hidden z-[70] flex items-center justify-center p-4 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden flex flex-col transform transition-all">
        <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-slate-50">
            <div>
                <h3 class="font-extrabold text-[#1a365d] text-base">Bukti Kehadiran (Selfie)</h3>
                <p class="text-[10px] text-gray-500 font-bold" id="ms-nama">-</p>
            </div>
            <button onclick="tutupModalSelfie()" class="text-gray-400 hover:text-red-500 bg-gray-100 hover:bg-red-50 w-8 h-8 rounded-full flex items-center justify-center transition-colors">&times;</button>
        </div>
        <div class="p-0 bg-black flex justify-center items-center relative">
            <img id="ms-img" src="" alt="Foto Selfie" class="max-h-[60vh] object-contain w-full">
            <div class="absolute bottom-4 left-4 bg-black/60 text-white backdrop-blur-sm px-3 py-1.5 rounded-lg border border-white/20">
                <p class="text-[10px] font-bold tracking-wider uppercase text-emerald-400">Waktu Presensi</p>
                <p class="text-sm font-extrabold" id="ms-jam">-</p>
            </div>
        </div>
        <div class="p-4 border-t border-gray-100 bg-white text-right">
            <button onclick="tutupModalSelfie()" class="bg-gray-800 text-white px-6 py-2 rounded-xl text-sm font-bold shadow-sm hover:bg-gray-900 transition-colors">Tutup Gambar</button>
        </div>
    </div>
</div>

<script>
// ---------- FUNGSI MODAL SELFIE ----------
function lihatSelfie(url, nama, jam) {
    document.getElementById('ms-img').src = url;
    document.getElementById('ms-nama').innerText = nama.toUpperCase();
    document.getElementById('ms-jam').innerText = jam + ' WIB';
    document.getElementById('modalSelfie').classList.remove('hidden');
}

function tutupModalSelfie() {
    document.getElementById('modalSelfie').classList.add('hidden');
    document.getElementById('ms-img').src = ''; // Clear image
}

// ---------- FUNGSI COPY LINK PUBLIK ----------
function copyLinkAbsensi(link) {
    navigator.clipboard.writeText(link).then(() => {
        Swal.fire({
            icon: 'success', title: 'Tersalin!', 
            text: 'Link portal absensi berhasil disalin ke clipboard. Silakan bagikan ke grup peserta.', 
            timer: 3000, showConfirmButton: false, toast: true, position: 'top-end', 
            customClass: { popup: 'rounded-xl' }
        });
    });
}
// Konfirmasi Tutup / Buka Paksa
function konfirmasiToggle(button) {
    Swal.fire({
        title: 'Ubah Status Absensi?',
        text: "Peserta akan terpengaruh oleh perubahan ini.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#1a365d',
        cancelButtonColor: '#ef4444',
        confirmButtonText: 'Ya, Ubah Status',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            button.closest('form').submit();
        }
    });
}

// Konfirmasi Reset Absensi
function konfirmasiReset(button, nama) {
    Swal.fire({
        title: 'Reset Absensi?',
        html: `Anda yakin ingin menghapus data absensi dan foto selfie atas nama <b>${nama}</b>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#9ca3af',
        confirmButtonText: 'Ya, Reset Data',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            button.closest('form').submit();
        }
    });
}
// ---------- EXPORT SHEETJS ----------
function exportDataKeExcel() {
    const dataRaw = @json($semuaDataExport);
    
    if(dataRaw.length === 0) {
        Swal.fire('Gagal!', 'Tidak ada data untuk diunduh.', 'error'); return;
    }

    const dataExcel = dataRaw.map((p, index) => {
        let namaLengkap = (p.gelar_depan ? p.gelar_depan + ' ' : '') + p.nama + (p.gelar_belakang ? ' ' + p.gelar_belakang : '');
        let statusHadir = p.absensi ? 'HADIR' : 'BELUM HADIR';
        let jamHadir = p.absensi ? new Date(p.absensi.jam_masuk).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'}) + ' WIB' : '-';
        
        return {
            'No': index + 1,
            'Nama Lengkap': namaLengkap,
            'NIK': p.nik,
            'Email': p.email_plataran_sehat,
            'Instansi': p.instansi,
            'Status Kehadiran': statusHadir,
            'Jam Masuk': jamHadir
        };
    });

    const worksheet = XLSX.utils.json_to_sheet(dataExcel);
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, "Rekap Absensi");
    
    XLSX.writeFile(workbook, "Rekap_Absensi_{{ Str::slug($event->nama_event) }}.xlsx");
}
</script>
@endsection