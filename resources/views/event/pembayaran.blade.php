@extends('layouts.admin')
@section('title', 'Pembayaran - ' . $event->nama_event)

@section('content')

<!-- Include SheetJS (XLSX) CDN -->
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>

@if(session('success'))
<script>document.addEventListener('DOMContentLoaded', () => Swal.fire({icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", timer: 2200, showConfirmButton: false, customClass: {popup: 'rounded-2xl'}}));</script>
@endif
@if($errors->any())
<script>document.addEventListener('DOMContentLoaded', () => Swal.fire({icon: 'error', title: 'Gagal!', html: `{!! implode('<br>', $errors->all()) !!}`, customClass: {popup: 'rounded-2xl'}}));</script>
@endif

<div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-end gap-4">
    <div>
        <a href="{{ route('event.index') }}" class="text-xs font-bold text-slate-400 hover:text-[#1a365d] flex items-center gap-1 mb-2">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Daftar Event
        </a>
        <h2 class="text-3xl font-extrabold text-[#1a365d]">Pembayaran Peserta</h2>
        <p class="text-gray-500 text-sm mt-1">Verifikasi pendaftaran dan status pembayaran peserta untuk event ini.</p>
    </div>
</div>

<!-- ============ DASHBOARD REKAP KEUANGAN ============ -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <!-- Total Seharusnya (Estimasi Pendapatan) -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="bg-indigo-100 text-indigo-600 p-3 rounded-xl">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"></path></svg>
        </div>
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Total Seharusnya</p>
            <p class="text-lg font-extrabold text-[#1a365d]">Rp {{ number_format($totalSeharusnya, 0, ',', '.') }}</p>
            <p class="text-[10px] font-bold text-indigo-400 mt-0.5">Dari {{ $pesertaAktifCount }} Peserta Aktif</p>
        </div>
    </div>

    <!-- Total Uang Masuk Keseluruhan -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="bg-blue-100 text-blue-600 p-3 rounded-xl">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Total Uang Masuk</p>
            <p class="text-lg font-extrabold text-[#1a365d]">Rp {{ number_format($totalUangMasuk, 0, ',', '.') }}</p>
            <p class="text-[10px] font-bold text-blue-400 mt-0.5">Total Realisasi Saat Ini</p>
        </div>
    </div>

    <!-- Total Lunas -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="bg-emerald-100 text-emerald-600 p-3 rounded-xl">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Total Uang Lunas</p>
            <p class="text-lg font-extrabold text-emerald-600">Rp {{ number_format($totalUangLunas, 0, ',', '.') }}</p>
            <p class="text-[10px] font-bold text-emerald-500 mt-0.5">Dari {{ $countLunas }} Orang Lunas</p>
        </div>
    </div>

    <!-- Total Kekurangan / Piutang -->
    <div onclick="bukaModalKurang()" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4 cursor-pointer hover:bg-orange-50 hover:border-orange-200 transition duration-200 group" title="Klik untuk melihat peserta yang belum lunas">
        <div class="bg-orange-100 text-orange-600 p-3 rounded-xl group-hover:bg-orange-500 group-hover:text-white transition">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        </div>
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide">Total Piutang</p>
            <p class="text-lg font-extrabold text-orange-600">Rp {{ number_format($totalUangKurang, 0, ',', '.') }}</p>
            <p class="text-[10px] font-bold text-orange-500 mt-0.5 underline">Lihat {{ $countCicil }} Orang Cicil ➜</p>
        </div>
    </div>
</div>

<!-- ============ DETAIL PELATIHAN ============ -->
@php
    $detailItems = array_filter([
        ['label' => 'Nama Pelatihan', 'value' => optional($event->pelatihan)->nama_pelatihan],
        ['label' => 'Nama Event', 'value' => $event->nama_event],
        ['label' => 'Batch / Tahun', 'value' => ($event->batch ? 'Batch '.$event->batch.' - ' : '').$event->tahun],
        ['label' => 'Tipe / Jenis', 'value' => $event->tipe_pelatihan.' ('.$event->jenis_pelatihan.')'],
        ['label' => 'Sistem / Lokasi', 'value' => $event->sistem_pelatihan.($event->lokasi ? ' - '.$event->lokasi : '')],
        ['label' => 'Jadwal', 'value' => \Carbon\Carbon::parse($event->tanggal_mulai)->format('d M Y').' s/d '.\Carbon\Carbon::parse($event->tanggal_selesai)->format('d M Y')],
        ['label' => 'SKP', 'value' => $event->skp ? $event->skp.' SKP' : null],
        ['label' => 'Biaya Pelatihan', 'value' => 'Rp '.number_format($event->biaya_pelatihan, 0, ',', '.')],
    ], fn($i) => !empty($i['value']));
@endphp

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 md:p-6 mb-6">
    <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-widest mb-4">Detail Pelatihan</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
        @foreach($detailItems as $item)
        <div class="border-l-4 border-blue-100 pl-3">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">{{ $item['label'] }}</p>
            <p class="text-sm font-extrabold text-slate-800 mt-0.5">{{ $item['value'] }}</p>
        </div>
        @endforeach
    </div>
</div>

<!-- ============ FILTER & PENCARIAN (STYLING BARU) ============ -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 md:p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-widest">Pencarian & Filter Data</h3>
        
        <!-- Tombol Unduh Data dipindah ke atas agar layout form rapi -->
        <button type="button" onclick="exportDataKeExcel()" class="bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white px-4 py-2 rounded-xl text-xs font-bold transition-colors flex items-center gap-1.5 border border-emerald-200 hover:border-emerald-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Export SheetJS
        </button>
    </div>

    <form action="{{ route('event.pembayaran', $event->id) }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
        
        <!-- Field Pencarian -->
        <div class="md:col-span-5">
            <label class="block text-xs font-bold text-slate-600 mb-2">Cari Nama, NIK, atau Email</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik kata kunci pencarian..." class="pl-10 w-full rounded-xl border-gray-200 text-sm py-2.5 focus:ring-[#1a365d] focus:border-[#1a365d] transition-shadow bg-slate-50 focus:bg-white">
            </div>
        </div>

        <!-- Field Status -->
        <div class="md:col-span-3">
            <label class="block text-xs font-bold text-slate-600 mb-2">Status Pembayaran</label>
            <select name="status" class="w-full rounded-xl border-gray-200 text-sm py-2.5 focus:ring-[#1a365d] focus:border-[#1a365d] bg-slate-50 focus:bg-white transition-shadow cursor-pointer">
                <option value="Semua" {{ request('status') == 'Semua' ? 'selected' : '' }}>Semua Status</option>
                <option value="Lunas" {{ request('status') == 'Lunas' ? 'selected' : '' }}>Lunas</option>
                <option value="Cicil" {{ request('status') == 'Cicil' ? 'selected' : '' }}>Cicil / Kurang</option>
                <option value="Belum Bayar" {{ request('status') == 'Belum Bayar' ? 'selected' : '' }}>Belum Bayar</option>
            </select>
        </div>

        <!-- Field Urutkan -->
        <div class="md:col-span-2">
            <label class="block text-xs font-bold text-slate-600 mb-2">Urutan Daftar</label>
            <select name="sort" class="w-full rounded-xl border-gray-200 text-sm py-2.5 focus:ring-[#1a365d] focus:border-[#1a365d] bg-slate-50 focus:bg-white transition-shadow cursor-pointer">
                <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Terbaru (Z-A)</option>
                <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Terlama (A-Z)</option>
            </select>
        </div>

        <!-- Tombol Terapkan -->
        <div class="md:col-span-2">
            <button type="submit" class="w-full bg-[#1a365d] text-white py-2.5 rounded-xl text-sm font-bold shadow-sm hover:bg-[#122643] transition-colors flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                Terapkan Filter
            </button>
        </div>
    </form>
</div>

<!-- ============ DAFTAR PESERTA UTAMA ============ -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
    <div class="p-4 bg-slate-50 border-b border-gray-100">
        <p class="text-xs text-slate-500 font-bold"><span class="text-blue-600">💡 Tips:</span> Klik pada baris nama peserta untuk melihat biodata & detail lengkap pendaftarannya.</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-white text-[#1a365d] text-xs uppercase tracking-wider border-b border-gray-100">
                    <th class="p-4 font-extrabold">Peserta</th>
                    <th class="p-4 font-extrabold">Instansi</th>
                    <th class="p-4 font-extrabold text-center">Status Pendaftaran</th>
                    <th class="p-4 font-extrabold text-center">Status Pembayaran</th>
                    <th class="p-4 font-extrabold">Rincian Bayar</th>
                    <th class="p-4 font-extrabold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
                @forelse($pesertas as $p)
                <!-- TR Row Bisa Di Klik -> Memanggil fungsi lihatDetailPeserta() -->
               <tr class="hover:bg-blue-50/50 cursor-pointer transition-colors border-b border-gray-50 align-top group" 
    data-detail="{{ json_encode($p) }}" 
    onclick="lihatDetailPeserta(this)">
                    
                    <td class="p-4 relative">
                        <div class="font-extrabold text-slate-800 group-hover:text-blue-700 transition-colors">
                            {{ trim($p->gelar_depan . ' ' . $p->nama . ' ' . $p->gelar_belakang) }}
                        </div>
                        <div class="text-xs text-gray-400 mt-0.5">{{ $p->email_plataran_sehat }}</div>
                        <div class="text-[10px] bg-slate-100 px-2 py-0.5 rounded text-gray-500 inline-block mt-1 border border-slate-200">
                            NIK: {{ $p->nik }}
                        </div>
                    </td>
                    <td class="p-4">
                        <div class="font-bold text-slate-700">{{ $p->instansi }}</div>
                        <div class="text-xs text-gray-400">{{ $p->departemen ?? '-' }}</div>
                    </td>
                    <td class="p-4 text-center">
                        @php
                            $b = [
                                'Menunggu' => 'bg-amber-100 text-amber-700 border-amber-200',
                                'Diterima' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                'Ditolak'  => 'bg-red-100 text-red-600 border-red-200',
                            ][$p->status_pendaftaran] ?? 'bg-gray-100 text-gray-700';
                        @endphp
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full border {{ $b }}">{{ $p->status_pendaftaran }}</span>
                    </td>
                    <td class="p-4 text-center">
                        @php
                            $bb = [
                                'Belum Bayar' => 'bg-slate-100 text-slate-500 border-slate-200',
                                'Cicil' => 'bg-orange-100 text-orange-700 border-orange-200',
                                'Lunas' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                            ][$p->status_pembayaran] ?? 'bg-gray-100';
                        @endphp
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full border {{ $bb }}">{{ $p->status_pembayaran }}</span>
                    </td>
                    <td class="p-4">
                        <div class="text-xs font-bold text-slate-700">Dibayar: Rp {{ number_format($p->total_dibayar, 0, ',', '.') }}</div>
                        @if($p->status_pembayaran === 'Cicil')
                        <div class="text-xs font-bold text-orange-600 mt-0.5">Kurang: Rp {{ number_format(max($event->biaya_pelatihan - $p->total_dibayar, 0), 0, ',', '.') }}</div>
                        @endif
                    </td>
                    
                    <!-- Kolom Aksi -> onclick="event.stopPropagation()" agar klik tombol tidak memicu Modal Detail Peserta -->
                    <td class="p-4" onclick="event.stopPropagation()">
                        <div class="flex flex-col gap-1.5 items-stretch w-40">
                            @if($p->status_pendaftaran === 'Menunggu')
                                <button type="button" onclick="accPeserta('{{ $p->id }}', {{ $event->biaya_pelatihan }})" class="bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-2 rounded-lg text-xs font-bold shadow-sm transition-colors">ACC Pendaftaran</button>
                                <button type="button" onclick="tolakPeserta('{{ $p->id }}', '{{ addslashes($p->nama) }}')" class="bg-red-50 hover:bg-red-500 hover:text-white text-red-600 px-3 py-2 rounded-lg text-xs font-bold border border-red-200 transition-colors">Tolak</button>
                            @elseif($p->status_pendaftaran === 'Diterima' && $p->status_pembayaran === 'Cicil')
                                @php $sisa = max($event->biaya_pelatihan - $p->total_dibayar, 0); @endphp
                                <button type="button" onclick="copyLinkPelunasan('{{ $p->link_pembayaran ?? '' }}')" class="bg-blue-50 hover:bg-blue-600 hover:text-white text-blue-700 px-3 py-2 rounded-lg text-xs font-bold border border-blue-200 transition-colors">Salin Link Pelunasan</button>
                                <button type="button" onclick="updateCicilan('{{ $p->id }}', {{ $event->biaya_pelatihan }}, {{ $sisa }})" class="bg-amber-50 hover:bg-amber-500 hover:text-white text-amber-700 px-3 py-2 rounded-lg text-xs font-bold border border-amber-200 transition-colors">Update Kekurangan</button>
                                <button type="button" onclick="tandaiLunas('{{ $p->id }}', '{{ addslashes($p->nama) }}')" class="bg-emerald-50 hover:bg-emerald-500 hover:text-white text-emerald-700 px-3 py-2 rounded-lg text-xs font-bold border border-emerald-200 transition-colors">Tandai Lunas</button>
                            @elseif($p->status_pendaftaran === 'Diterima' && $p->status_pembayaran === 'Lunas')
                                <span class="text-xs font-bold text-emerald-600 text-center py-2 bg-emerald-50 rounded-lg border border-emerald-100">✔ Selesai Lunas</span>
                            @elseif($p->status_pendaftaran === 'Ditolak')
                                <span class="text-xs font-bold text-gray-400 text-center py-2 bg-gray-50 rounded-lg border border-gray-200">Ditolak</span>
                            @endif
                        </div>

                        <!-- Form Aksi Hidden -->
                        <form id="acc-form-{{ $p->id }}" action="{{ route('registrasi.acc', $p->id) }}" method="POST" class="hidden">
                            @csrf <input type="hidden" name="keputusan" id="keputusan-{{ $p->id }}">
                            <input type="hidden" name="kekurangan" id="kekurangan-{{ $p->id }}">
                        </form>
                        <form id="tolak-form-{{ $p->id }}" action="{{ route('registrasi.tolak', $p->id) }}" method="POST" class="hidden">@csrf</form>
                        <form id="update-form-{{ $p->id }}" action="{{ route('registrasi.update-cicilan', $p->id) }}" method="POST" class="hidden">
                            @csrf <input type="hidden" name="kekurangan" id="update-kekurangan-{{ $p->id }}">
                        </form>
                        <form id="lunas-form-{{ $p->id }}" action="{{ route('registrasi.lunas', $p->id) }}" method="POST" class="hidden">@csrf</form>
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


<!-- ============ MODAL 1: LIST PESERTA KURANG (PIUTANG) DENGAN PENCARIAN ============ -->
<div id="modalKurang" class="fixed inset-0 bg-slate-900/60 hidden z-[60] flex items-center justify-center p-4 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-2xl w-full max-w-4xl shadow-2xl overflow-hidden flex flex-col max-h-[85vh]">
        <div class="p-5 border-b border-gray-100 bg-white flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="font-extrabold text-[#1a365d] text-xl">Daftar Piutang / Cicilan ({{ $countCicil }} Orang)</h3>
                <p class="text-xs text-gray-500 mt-1">Cari berdasarkan nama peserta yang belum lunas.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <div class="relative">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input type="text" id="searchPiutang" onkeyup="cariPiutang()" placeholder="Cari nama peserta..." class="pl-9 text-sm rounded-xl border-gray-200 py-2 w-full md:w-64 focus:ring-orange-500 focus:border-orange-500 bg-slate-50 focus:bg-white">
                </div>
                <button onclick="tutupModalKurang()" class="text-gray-400 hover:text-red-500 bg-gray-100 hover:bg-red-50 w-9 h-9 rounded-full flex items-center justify-center transition-colors">&times;</button>
            </div>
        </div>
        
        <div class="overflow-y-auto p-5 flex-1 bg-slate-50/50">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <table class="w-full text-left border-collapse text-sm" id="tabelPiutang">
                    <thead>
                        <tr class="border-b border-gray-200 bg-slate-50">
                            <th class="p-4 text-slate-500 font-bold uppercase text-xs tracking-wider">Nama / Instansi</th>
                            <th class="p-4 text-slate-500 font-bold uppercase text-xs tracking-wider">Kontak (Email)</th>
                            <th class="p-4 text-slate-500 font-bold uppercase text-xs tracking-wider">Sudah Bayar</th>
                            <th class="p-4 text-slate-500 font-bold uppercase text-xs tracking-wider">Sisa Kurang</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pesertaKurang as $pk)
                        <tr class="border-b border-gray-50 item-piutang hover:bg-orange-50/30 transition-colors">
                            <td class="p-4">
                                <div class="font-bold text-slate-800 nama-peserta">{{ trim($pk->gelar_depan . ' ' . $pk->nama . ' ' . $pk->gelar_belakang) }}</div>
                                <div class="text-xs text-gray-500">{{ $pk->instansi }}</div>
                            </td>
                            <td class="p-4 text-slate-600 text-xs">
                                {{ $pk->email_plataran_sehat }}
                            </td>
                            <td class="p-4 text-emerald-600 font-extrabold">Rp {{ number_format($pk->total_dibayar, 0, ',', '.') }}</td>
                            <td class="p-4 text-orange-600 font-extrabold">Rp {{ number_format(max($event->biaya_pelatihan - $pk->total_dibayar, 0), 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center py-10 text-gray-500 font-bold">Tidak ada peserta yang menunggak cicilan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="p-4 border-t border-gray-100 bg-white text-right">
            <button onclick="tutupModalKurang()" class="bg-gray-800 text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-sm hover:bg-gray-900 transition-colors">Tutup Modal</button>
        </div>
    </div>
</div>

<!-- ============ MODAL 2: DETAIL LENGKAP PESERTA ============ -->
<div id="modalDetailPeserta" class="fixed inset-0 bg-slate-900/70 hidden z-[70] flex items-center justify-center p-4 backdrop-blur-md transition-opacity">
    <div class="bg-white rounded-2xl w-full max-w-4xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
        
        <!-- Header Modal -->
        <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-[#1a365d] to-[#254f8a] text-white flex justify-between items-center relative overflow-hidden">
            <div class="absolute right-0 top-0 opacity-10">
                <svg class="w-32 h-32 transform translate-x-8 -translate-y-8" fill="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
            </div>
            <div class="relative z-10">
                <h3 class="font-extrabold text-2xl" id="mdl-nama-lengkap">-</h3>
                <div class="flex gap-2 mt-2">
                    <span class="text-[10px] bg-white/20 px-2.5 py-1 rounded-full font-bold tracking-widest uppercase" id="mdl-status-daftar">-</span>
                    <span class="text-[10px] bg-white/20 px-2.5 py-1 rounded-full font-bold tracking-widest uppercase" id="mdl-status-bayar">-</span>
                </div>
            </div>
            <button onclick="tutupModalDetail()" class="relative z-10 text-white/70 hover:text-white bg-white/10 hover:bg-white/20 w-10 h-10 rounded-full flex items-center justify-center transition-colors">&times;</button>
        </div>
        
        <!-- Body Modal -->
        <div class="overflow-y-auto p-6 flex-1 bg-slate-50/50">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Kolom 1: Data Pribadi & Pekerjaan -->
                <div class="space-y-6">
                    <!-- Section Personal -->
                    <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                        <h4 class="text-xs font-extrabold text-blue-600 uppercase tracking-widest mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Informasi Pribadi
                        </h4>
                        <div class="space-y-3">
                            <div><p class="text-[10px] text-gray-400 font-bold uppercase">NIK</p><p class="text-sm font-bold text-slate-800" id="mdl-nik">-</p></div>
                            <div><p class="text-[10px] text-gray-400 font-bold uppercase">Tempat, Tanggal Lahir</p><p class="text-sm font-bold text-slate-800" id="mdl-ttl">-</p></div>
                            <div><p class="text-[10px] text-gray-400 font-bold uppercase">Alamat Lengkap</p><p class="text-sm font-bold text-slate-800" id="mdl-alamat">-</p></div>
                            <div><p class="text-[10px] text-gray-400 font-bold uppercase">Ukuran Kaos</p><p class="text-sm font-bold text-slate-800" id="mdl-kaos">-</p></div>
                        </div>
                    </div>

                    <!-- Section Pekerjaan -->
                    <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                        <h4 class="text-xs font-extrabold text-indigo-600 uppercase tracking-widest mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            Pekerjaan & Instansi
                        </h4>
                        <div class="space-y-3">
                            <div><p class="text-[10px] text-gray-400 font-bold uppercase">Instansi</p><p class="text-sm font-bold text-slate-800" id="mdl-instansi">-</p></div>
                            <div><p class="text-[10px] text-gray-400 font-bold uppercase">Departemen / Unit</p><p class="text-sm font-bold text-slate-800" id="mdl-departemen">-</p></div>
                            <div><p class="text-[10px] text-gray-400 font-bold uppercase">NIP</p><p class="text-sm font-bold text-slate-800" id="mdl-nip">-</p></div>
                            <div><p class="text-[10px] text-gray-400 font-bold uppercase">Pangkat / Golongan</p><p class="text-sm font-bold text-slate-800" id="mdl-pangkat">-</p></div>
                        </div>
                    </div>
                </div>

                <!-- Kolom 2: Kontak & Pembayaran -->
                <div class="space-y-6">
                    <!-- Section Kontak -->
                    <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                        <h4 class="text-xs font-extrabold text-emerald-600 uppercase tracking-widest mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            Kontak / Plataran Sehat
                        </h4>
                        <div class="space-y-3">
                            <div><p class="text-[10px] text-gray-400 font-bold uppercase">Email Plataran Sehat</p><p class="text-sm font-bold text-slate-800" id="mdl-email">-</p></div>
                        </div>
                    </div>

                    <!-- Section Pembayaran -->
                    <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                        <h4 class="text-xs font-extrabold text-orange-600 uppercase tracking-widest mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            Bukti & Nominal Pembayaran
                        </h4>
                        
                        <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-3">
                            <span class="text-xs text-gray-500 font-bold uppercase">Total Dibayar</span>
                            <span class="text-lg font-extrabold text-emerald-600" id="mdl-uang-masuk">Rp 0</span>
                        </div>
                        
                        <div class="flex justify-between items-center border-b border-gray-100 pb-4 mb-4">
                            <span class="text-xs text-gray-500 font-bold uppercase">Sisa Kurang (Piutang)</span>
                            <span class="text-lg font-extrabold text-orange-600" id="mdl-uang-kurang">Rp 0</span>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <p class="text-[10px] text-gray-400 font-bold uppercase mb-2">Bukti Bayar (Awal)</p>
                                <div id="container-bukti-1">
                                    <!-- Diisi via JS -->
                                </div>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-400 font-bold uppercase mb-2">Bukti Pelunasan (Akhir)</p>
                                <div id="container-bukti-2">
                                    <!-- Diisi via JS -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        
        <div class="p-5 border-t border-gray-100 bg-white text-right flex justify-between items-center">
            <p class="text-xs text-gray-400 font-bold" id="mdl-waktu-daftar">Waktu Daftar: -</p>
            <button onclick="tutupModalDetail()" class="bg-gray-800 text-white px-8 py-2.5 rounded-xl text-sm font-bold shadow-sm hover:bg-gray-900 transition-colors">Tutup</button>
        </div>
    </div>
</div>

<script>
// ---------- FUNGSI DETAIL PESERTA (KLIK ROW) ----------
function formatRupiah(angka) {
    return 'Rp ' + parseInt(angka).toLocaleString('id-ID');
}

function formatDateIndo(dateString) {
    if(!dateString) return '-';
    const date = new Date(dateString);
    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    return `${date.getDate()} ${months[date.getMonth()]} ${date.getFullYear()}`;
}

function lihatDetailPeserta(element) {
    // Parsing data dari atribut data-detail di elemen <tr>
    const data = JSON.parse(element.getAttribute('data-detail'));
    const biayaPelatihan = {{ $event->biaya_pelatihan }};
    
    // Set Nama Lengkap
    let gelarDepan = data.gelar_depan ? data.gelar_depan + ' ' : '';
    let gelarBelakang = data.gelar_belakang ? ' ' + data.gelar_belakang : '';
    document.getElementById('mdl-nama-lengkap').innerText = gelarDepan + data.nama + gelarBelakang;

    // Set Status
    document.getElementById('mdl-status-daftar').innerText = data.status_pendaftaran;
    document.getElementById('mdl-status-bayar').innerText = data.status_pembayaran;

    // Set Data Pribadi
    document.getElementById('mdl-nik').innerText = data.nik || '-';
    document.getElementById('mdl-ttl').innerText = (data.tempat_lahir || '-') + ', ' + formatDateIndo(data.tanggal_lahir);
    document.getElementById('mdl-alamat').innerText = data.alamat_lengkap || '-';
    document.getElementById('mdl-kaos').innerText = data.ukuran_kaos ? 'Ukuran ' + data.ukuran_kaos.toUpperCase() : '-';

    // Set Data Pekerjaan
    document.getElementById('mdl-instansi').innerText = data.instansi || '-';
    document.getElementById('mdl-departemen').innerText = data.departemen || '-';
    document.getElementById('mdl-nip').innerText = data.nip || '-';
    document.getElementById('mdl-pangkat').innerText = data.pangkat_golongan || '-';

    // Set Kontak
    document.getElementById('mdl-email').innerText = data.email_plataran_sehat || '-';

    // Set Pembayaran
    document.getElementById('mdl-uang-masuk').innerText = formatRupiah(data.total_dibayar);
    
    let kurang = 0;
    if (data.status_pembayaran === 'Cicil') {
        kurang = Math.max(biayaPelatihan - data.total_dibayar, 0);
    } else if (data.status_pembayaran === 'Belum Bayar') {
        kurang = biayaPelatihan;
    }
    document.getElementById('mdl-uang-kurang').innerText = formatRupiah(kurang);

    // Set Bukti Bayar 1
    const container1 = document.getElementById('container-bukti-1');
    if (data.bukti_bayar_pertama) {
        let url1 = `{{ asset('storage') }}/${data.bukti_bayar_pertama}`;
        container1.innerHTML = `<a href="${url1}" target="_blank" class="block w-full overflow-hidden rounded-lg border border-gray-200 group"><img src="${url1}" class="w-full h-24 object-cover group-hover:scale-110 transition-transform duration-300" alt="Bukti 1"></a>`;
    } else {
        container1.innerHTML = `<div class="w-full h-24 bg-gray-50 border border-gray-200 rounded-lg flex items-center justify-center text-xs text-gray-400 font-bold border-dashed">Belum Ada</div>`;
    }

    // Set Bukti Bayar 2
    const container2 = document.getElementById('container-bukti-2');
    if (data.bukti_bayar_terakhir) {
        let url2 = `{{ asset('storage') }}/${data.bukti_bayar_terakhir}`;
        container2.innerHTML = `<a href="${url2}" target="_blank" class="block w-full overflow-hidden rounded-lg border border-gray-200 group"><img src="${url2}" class="w-full h-24 object-cover group-hover:scale-110 transition-transform duration-300" alt="Bukti 2"></a>`;
    } else {
        container2.innerHTML = `<div class="w-full h-24 bg-gray-50 border border-gray-200 rounded-lg flex items-center justify-center text-xs text-gray-400 font-bold border-dashed">Belum Ada</div>`;
    }

    // Waktu Daftar
    let tglDaftar = new Date(data.created_at);
    document.getElementById('mdl-waktu-daftar').innerText = "Waktu Daftar: " + tglDaftar.toLocaleString('id-ID');

    // Tampilkan Modal
    document.getElementById('modalDetailPeserta').classList.remove('hidden');
}

function tutupModalDetail() {
    document.getElementById('modalDetailPeserta').classList.add('hidden');
}


// ---------- FUNGSI PENCARIAN DI MODAL PIUTANG ----------
function cariPiutang() {
    let input = document.getElementById("searchPiutang").value.toLowerCase();
    let rows = document.querySelectorAll("#tabelPiutang .item-piutang");
    
    rows.forEach(row => {
        let nama = row.querySelector(".nama-peserta").innerText.toLowerCase();
        if (nama.includes(input)) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
}

// ---------- EXPORT SHEETJS ----------
function exportDataKeExcel() {
    const dataRaw = @json($semuaDataExport);
    if(dataRaw.length === 0) {
        Swal.fire('Gagal!', 'Tidak ada data untuk diunduh.', 'error'); return;
    }
    const biayaPelatihan = {{ $event->biaya_pelatihan }};
    const dataExcel = dataRaw.map(p => {
        let sisa = p.status_pembayaran === 'Cicil' || p.status_pembayaran === 'Belum Bayar' ? Math.max(biayaPelatihan - p.total_dibayar, 0) : 0;
        let namaLengkap = (p.gelar_depan ? p.gelar_depan + ' ' : '') + p.nama + (p.gelar_belakang ? ' ' + p.gelar_belakang : '');
        return {
            'Waktu Daftar': new Date(p.created_at).toLocaleString('id-ID'),
            'Nama Lengkap': namaLengkap,
            'NIK': p.nik,
            'Email': p.email_plataran_sehat,
            'Instansi': p.instansi,
            'Status Pendaftaran': p.status_pendaftaran,
            'Status Pembayaran': p.status_pembayaran,
            'Total Dibayar (Rp)': Number(p.total_dibayar),
            'Kekurangan (Rp)': sisa
        };
    });
    const worksheet = XLSX.utils.json_to_sheet(dataExcel);
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, "Data Pembayaran");
    XLSX.writeFile(workbook, "Laporan_Pembayaran_{{ Str::slug($event->nama_event) }}.xlsx");
}

// ---------- MODAL UANG KURANG ----------
function bukaModalKurang() { document.getElementById('modalKurang').classList.remove('hidden'); }
function tutupModalKurang() { document.getElementById('modalKurang').classList.add('hidden'); }

// ---------- FORMAT ANGKA (TITIK) ----------
function terapkanFormatRibuan(inputElement) {
    inputElement.addEventListener('input', function(e) {
        let nilai = this.value.replace(/[^0-9]/g, '');
        this.value = nilai !== '' ? parseInt(nilai, 10).toLocaleString('id-ID') : '';
    });
}

// ---------- FUNGSI AKSI PESERTA ----------
function accPeserta(id, biaya) {
    Swal.fire({
        title: 'Terima Pendaftaran Peserta',
        html: `<p class="text-sm text-gray-600">Pilih status pembayaran peserta ini:</p>`,
        showDenyButton: true, showCancelButton: true,
        confirmButtonText: 'Lunas', denyButtonText: 'Cicil', cancelButtonText: 'Batal',
        confirmButtonColor: '#059669', denyButtonColor: '#f59e0b',
        customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl px-6 py-2.5 font-bold', denyButton: 'rounded-xl px-6 py-2.5 font-bold', cancelButton: 'rounded-xl px-6 py-2.5 font-bold' }
    }).then((result) => {
        if (result.isConfirmed) {
            submitAcc(id, 'Lunas', null);
        } else if (result.isDenied) {
            Swal.fire({
                title: 'Sisa Kekurangan Pembayaran',
                html: `
                    <p class="text-xs text-gray-500 mb-3">Biaya Pelatihan: <b>Rp ${Number(biaya).toLocaleString('id-ID')}</b></p>
                    <input type="text" id="input-kekurangan-${id}" class="swal2-input !mt-0" placeholder="Misal: 200.000" style="text-align: center;">
                `,
                showCancelButton: true, confirmButtonText: 'Terima & Simpan', confirmButtonColor: '#1a365d', cancelButtonText: 'Batal',
                customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl px-6 py-2.5 font-bold', cancelButton: 'rounded-xl px-6 py-2.5 font-bold' },
                didOpen: () => { terapkanFormatRibuan(document.getElementById(`input-kekurangan-${id}`)); },
                preConfirm: () => {
                    const rawValue = document.getElementById(`input-kekurangan-${id}`).value.replace(/\./g, '');
                    if (!rawValue || isNaN(rawValue) || rawValue < 0) {
                        Swal.showValidationMessage('Masukkan jumlah kekurangan yang valid'); return false;
                    }
                    return rawValue;
                }
            }).then((res) => {
                if (res.isConfirmed) submitAcc(id, 'Cicil', res.value);
            });
        }
    });
}

function submitAcc(id, keputusan, kekurangan) {
    document.getElementById('keputusan-' + id).value = keputusan;
    document.getElementById('kekurangan-' + id).value = kekurangan ?? '';
    document.getElementById('acc-form-' + id).submit();
}

function updateCicilan(id, biaya, kekuranganLama) {
    Swal.fire({
        title: 'Update Sisa Kekurangan',
        html: `
            <p class="text-xs text-gray-500 mb-3">Biaya Pelatihan: <b>Rp ${Number(biaya).toLocaleString('id-ID')}</b></p>
            <input type="text" id="input-update-${id}" class="swal2-input !mt-0" value="${Number(kekuranganLama).toLocaleString('id-ID')}" style="text-align: center;">
        `,
        showCancelButton: true, confirmButtonText: 'Simpan', confirmButtonColor: '#1a365d', cancelButtonText: 'Batal',
        customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl px-6 py-2.5 font-bold', cancelButton: 'rounded-xl px-6 py-2.5 font-bold' },
        didOpen: () => { terapkanFormatRibuan(document.getElementById(`input-update-${id}`)); },
        preConfirm: () => {
            const rawValue = document.getElementById(`input-update-${id}`).value.replace(/\./g, '');
            if (!rawValue || isNaN(rawValue) || rawValue < 0) { Swal.showValidationMessage('Masukkan jumlah yang valid'); return false; }
            return rawValue;
        }
    }).then((res) => {
        if (res.isConfirmed) {
            document.getElementById('update-kekurangan-' + id).value = res.value;
            document.getElementById('update-form-' + id).submit();
        }
    });
}

function tolakPeserta(id, nama) {
    Swal.fire({
        title: 'Tolak Pendaftaran?', html: `Yakin menolak pendaftaran <b>${nama}</b>?`, icon: 'warning',
        showCancelButton: true, confirmButtonText: 'Ya, Tolak', confirmButtonColor: '#ef4444', cancelButtonText: 'Batal',
        customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl px-6 py-2.5 font-bold', cancelButton: 'rounded-xl px-6 py-2.5 font-bold' }
    }).then((result) => { if (result.isConfirmed) document.getElementById('tolak-form-' + id).submit(); });
}

function tandaiLunas(id, nama) {
    Swal.fire({
        title: 'Tandai Lunas?', html: `Pembayaran <b>${nama}</b> akan ditandai LUNAS.`, icon: 'question',
        showCancelButton: true, confirmButtonText: 'Ya, Tandai Lunas', confirmButtonColor: '#059669', cancelButtonText: 'Batal',
        customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl px-6 py-2.5 font-bold', cancelButton: 'rounded-xl px-6 py-2.5 font-bold' }
    }).then((result) => { if (result.isConfirmed) document.getElementById('lunas-form-' + id).submit(); });
}

function copyLinkPelunasan(link) {
    navigator.clipboard.writeText(link).then(() => {
        Swal.fire({ icon: 'success', title: 'Tersalin!', text: 'Link disalin ke clipboard.', timer: 2000, showConfirmButton: false, toast: true, position: 'top-end', customClass: { popup: 'rounded-xl' } });
    });
}
</script>
@endsection