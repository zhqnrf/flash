@extends('layouts.admin')
@section('title', 'Rekap Absensi Matriks - ' . $event->nama_event)

@section('content')

<!-- Include SheetJS (XLSX) CDN -->
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))
<script>document.addEventListener('DOMContentLoaded', () => Swal.fire({icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", timer: 2500, showConfirmButton: false, customClass: {popup: 'rounded-2xl'}}));</script>
@endif

<div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-end gap-4">
    <div>
        <a href="{{ route('event.index') }}" class="text-xs font-bold text-slate-400 hover:text-[#1a365d] flex items-center gap-1 mb-2">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Daftar Event
        </a>
        <h2 class="text-3xl font-extrabold text-[#1a365d]">Rekap Absensi Matriks</h2>
        <p class="text-gray-500 text-sm mt-1">{{ $totalHari }} Hari Pelatihan ({{ \Carbon\Carbon::parse($event->tanggal_mulai)->translatedFormat('d M') }} - {{ \Carbon\Carbon::parse($event->tanggal_selesai)->translatedFormat('d M Y') }})</p>
    </div>
    
    <div class="flex gap-2">
        <button onclick="copyLinkAbsensi('{{ route('presensi.public', $event->uuid) }}')" class="bg-white border border-blue-200 text-blue-700 hover:bg-blue-50 px-4 py-2 rounded-xl text-sm font-bold shadow-sm flex items-center gap-2 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
            Salin Link Presensi
        </button>
    </div>
</div>

<!-- ============ DASHBOARD STATISTIK & PORTAL ============ -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
  <!-- Status Portal Absensi (Mendapatkan porsi lebih besar) -->
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex flex-col justify-between relative overflow-hidden">
        <div class="flex items-center gap-3 mb-4">
            @if($event->status_absen === 'tutup_paksa')
                <div class="bg-red-100 text-red-600 p-2.5 rounded-xl"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg></div>
                <div><p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Status Portal</p><p class="text-base font-extrabold text-red-600 leading-tight">Ditutup Paksa</p></div>
            @elseif($event->status_absen === 'buka_paksa')
                <div class="bg-emerald-100 text-emerald-600 p-2.5 rounded-xl"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg></div>
                <div><p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Status Portal</p><p class="text-base font-extrabold text-emerald-600 leading-tight">Dibuka Paksa</p></div>
            @else
                <div class="bg-blue-100 text-blue-600 p-2.5 rounded-xl"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                <div><p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Mode Otomatis</p>
                    @if($bukaAbsensi) <p class="text-base font-extrabold text-emerald-600 leading-tight">Sedang Buka</p>
                    @else <p class="text-base font-extrabold text-amber-600 leading-tight">Luar Jam/Tanggal</p> @endif
                </div>
            @endif
        </div>
        
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

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex flex-col justify-center">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Diterima</p>
        <p class="text-3xl font-extrabold text-[#1a365d]">{{ $totalDiterima }}</p>
    </div>
    <div class="bg-emerald-50 rounded-2xl shadow-sm border border-emerald-100 p-5 flex flex-col justify-center">
        <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-wide">Hadir Penuh</p>
        <p class="text-3xl font-extrabold text-emerald-600">{{ $totalHadirLengkap }}</p>
    </div>
    <div class="bg-red-50 rounded-2xl shadow-sm border border-red-100 p-5 flex flex-col justify-center">
        <p class="text-[10px] font-bold text-red-600 uppercase tracking-wide">Tidak Pernah Hadir</p>
        <p class="text-3xl font-extrabold text-red-600">{{ $totalTidakHadir }}</p>
    </div>
</div>

<!-- ============ FILTER & PENCARIAN (SERVER SIDE) ============ -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 md:p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-widest">Filter Matriks Kehadiran</h3>
        
        <button type="button" onclick="exportDataKeExcel()" class="bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white px-4 py-2 rounded-xl text-xs font-bold transition-colors flex items-center gap-1.5 border border-emerald-200 hover:border-emerald-500 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Export Rekap (Excel)
        </button>
    </div>

    <form action="{{ route('event.absensi', $event->id) }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
        <div class="md:col-span-5">
            <label class="block text-xs font-bold text-slate-600 mb-2">Cari Nama atau Instansi</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg></div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik kata kunci..." class="pl-10 w-full rounded-xl border-gray-200 text-sm py-2.5 focus:ring-[#1a365d] bg-slate-50 focus:bg-white">
            </div>
        </div>
        <div class="md:col-span-3">
            <label class="block text-xs font-bold text-slate-600 mb-2">Status Total Kehadiran</label>
            <select name="status" class="w-full rounded-xl border-gray-200 text-sm py-2.5 focus:ring-[#1a365d] bg-slate-50 focus:bg-white cursor-pointer">
                <option value="Semua" {{ request('status') == 'Semua' ? 'selected' : '' }}>Semua Status</option>
                <option value="Lengkap" {{ request('status') == 'Lengkap' ? 'selected' : '' }}>Hadir Lengkap ({{ $totalHari }} Hari)</option>
                <option value="Sebagian" {{ request('status') == 'Sebagian' ? 'selected' : '' }}>Hadir Sebagian</option>
                <option value="Tidak" {{ request('status') == 'Tidak' ? 'selected' : '' }}>Tidak Pernah Hadir</option>
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

<!-- ============ TABEL DAFTAR MATRIKS ABSENSI ============ -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse whitespace-nowrap min-w-[800px]">
            <thead>
                <tr class="bg-slate-50 text-[#1a365d] text-xs uppercase tracking-wider border-b border-gray-100">
                    <th class="p-4 font-extrabold w-12 text-center">No</th>
                    <th class="p-4 font-extrabold w-64">Nama & Instansi</th>
                    @foreach($daftarTanggal as $tgl)
                        <th class="p-4 font-extrabold text-center">{{ $tgl->translatedFormat('d M') }}</th>
                    @endforeach
                    <th class="p-4 font-extrabold text-center border-l border-gray-100">Total Hadir</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
                @forelse($pesertas as $i => $p)
                <tr class="hover:bg-blue-50/40 border-b border-gray-50 transition-colors">
                    <td class="p-4 text-center text-gray-400 font-bold align-top pt-5">{{ $pesertas->firstItem() + $i }}</td>
                    <td class="p-4 align-top pt-5">
                        <div class="font-extrabold text-slate-800 whitespace-normal">{{ $p->nama_lengkap }}</div>
                        <div class="text-xs text-gray-500 mt-1 whitespace-normal">{{ $p->instansi }}</div>
                    </td>
                    
                    @foreach($p->rekap_harian as $h)
                    <td class="p-4 text-center align-top pt-5">
                        @if($h['hadir'])
                            <div class="bg-emerald-50 border border-emerald-100 rounded-lg p-2 inline-flex flex-col items-center min-w-[80px]">
                                <span class="text-emerald-600 font-extrabold text-sm">{{ $h['jam'] }}</span>
                                <div class="flex items-center gap-2 mt-2">
                                    <button type="button" onclick="lihatSelfie('{{ asset('storage/'.$h['foto']) }}', '{{ addslashes($p->nama) }}', '{{ $h['jam'] }}')" class="text-blue-500 hover:text-blue-700 p-1" title="Lihat Selfie">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                    <form action="{{ route('absensi.reset', $h['id_absen']) }}" method="POST" class="inline-block">
                                        @csrf @method('DELETE')
                                        <button type="button" onclick="konfirmasiReset(this, '{{ addslashes($p->nama) }}')" class="text-red-400 hover:text-red-600 p-1" title="Hapus Kehadiran Ini">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="text-gray-300 font-bold py-2">-</div>
                        @endif
                    </td>
                    @endforeach

                    <td class="p-4 text-center border-l border-gray-100 align-middle">
                        @php
                            $badgeColor = $p->total_hadir == $totalHari ? 'bg-emerald-100 text-emerald-700 border-emerald-200' 
                                        : ($p->total_hadir > 0 ? 'bg-amber-100 text-amber-700 border-amber-200' : 'bg-red-50 text-red-600 border-red-200');
                        @endphp
                        <span class="text-xs font-bold px-3 py-1.5 rounded-full border {{ $badgeColor }}">
                            {{ $p->total_hadir }} / {{ $totalHari }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ 3 + $totalHari }}" class="py-16 text-center">
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
                <p class="text-[10px] font-bold tracking-wider uppercase text-emerald-400">Jam Presensi</p>
                <p class="text-sm font-extrabold" id="ms-jam">-</p>
            </div>
        </div>
        <div class="p-4 border-t border-gray-100 bg-white text-right">
            <button onclick="tutupModalSelfie()" class="bg-gray-800 text-white px-6 py-2 rounded-xl text-sm font-bold shadow-sm hover:bg-gray-900 transition-colors">Tutup Gambar</button>
        </div>
    </div>
</div>

<script>
function lihatSelfie(url, nama, jam) {
    document.getElementById('ms-img').src = url;
    document.getElementById('ms-nama').innerText = nama.toUpperCase();
    document.getElementById('ms-jam').innerText = jam + ' WIB';
    document.getElementById('modalSelfie').classList.remove('hidden');
}

function tutupModalSelfie() {
    document.getElementById('modalSelfie').classList.add('hidden');
    document.getElementById('ms-img').src = ''; 
}

function copyLinkAbsensi(link) {
    navigator.clipboard.writeText(link).then(() => {
        Swal.fire({
            icon: 'success', title: 'Tersalin!', 
            text: 'Link portal absensi berhasil disalin ke clipboard.', 
            timer: 3000, showConfirmButton: false, toast: true, position: 'top-end', 
            customClass: { popup: 'rounded-xl' }
        });
    });
}

function konfirmasiReset(button, nama) {
    Swal.fire({
        title: 'Hapus Kehadiran?',
        html: `Anda yakin ingin menghapus data absen hari ini untuk <b>${nama}</b>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#9ca3af',
        confirmButtonText: 'Ya, Hapus Data',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            button.closest('form').submit();
        }
    });
}

function exportDataKeExcel() {
    // Ambil data utuh (tidak terpaginasi) dari controller
    const dataRaw = @json($semuaDataExport);
    
    if(dataRaw.length === 0) {
        Swal.fire('Gagal!', 'Tidak ada data untuk diunduh.', 'error'); return;
    }

    // Bangun struktur Header dinamis
    const headerRow = ['No', 'Nama Lengkap', 'NIK', 'Email', 'Instansi'];
    const totalHari = {{ $totalHari }};
    
    // Asumsi rekap_harian index 0 memiliki tanggal_label yang benar
    if (dataRaw[0] && dataRaw[0].rekap_harian) {
        dataRaw[0].rekap_harian.forEach(h => {
            headerRow.push(h.tanggal_label);
        });
    }
    headerRow.push('Total Hadir');

    const dataExcel = [headerRow];

    // Isi Baris
    dataRaw.forEach((p, index) => {
        let namaLengkap = (p.gelar_depan ? p.gelar_depan + ' ' : '') + p.nama + (p.gelar_belakang ? ' ' + p.gelar_belakang : '');
        
        let row = [
            index + 1,
            namaLengkap,
            p.nik,
            p.email_plataran_sehat,
            p.instansi
        ];

        // Looping per hari kehadiran
        p.rekap_harian.forEach(h => {
            row.push(h.jam); 
        });

        row.push(p.total_hadir + '/' + totalHari);
        dataExcel.push(row);
    });

    const worksheet = XLSX.utils.aoa_to_sheet(dataExcel);
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, "Matriks Rekap Absensi");
    
    XLSX.writeFile(workbook, "Rekap_Matriks_{{ Str::slug($event->nama_event) }}.xlsx");
}
</script>
@endsection