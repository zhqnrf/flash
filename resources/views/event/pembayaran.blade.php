@extends('layouts.admin')
@section('title', 'Pembayaran - ' . $event->nama_event)

@section('content')

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

<!-- ============ DETAIL PELATIHAN ============ -->
@php
    $detailItems = array_filter([
        ['label' => 'Nama Pelatihan (Master)', 'value' => optional($event->pelatihan)->nama_pelatihan],
        ['label' => 'Nama Event', 'value' => $event->nama_event],
        ['label' => 'Batch / Tahun', 'value' => ($event->batch ? 'Batch '.$event->batch.' - ' : '').$event->tahun],
        ['label' => 'Tipe / Jenis', 'value' => $event->tipe_pelatihan.' ('.$event->jenis_pelatihan.')'],
        ['label' => 'Sistem / Lokasi', 'value' => $event->sistem_pelatihan.($event->lokasi ? ' - '.$event->lokasi : '')],
        ['label' => 'Jadwal', 'value' => \Carbon\Carbon::parse($event->tanggal_mulai)->format('d M Y').' s/d '.\Carbon\Carbon::parse($event->tanggal_selesai)->format('d M Y')],
        ['label' => 'SKP', 'value' => $event->skp ? $event->skp.' SKP' : null],
        ['label' => 'Biaya Pelatihan', 'value' => 'Rp '.number_format($event->biaya_pelatihan, 0, ',', '.')],
        ['label' => 'Rekening Pembayaran', 'value' => $event->rekening_pembayaran],
    ], fn($i) => !empty($i['value']));
@endphp

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 md:p-6 mb-6">
    <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-widest mb-4">Detail Pelatihan</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
        @foreach($detailItems as $item)
        <div class="border-l-4 border-blue-100 pl-3">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">{{ $item['label'] }}</p>
            <p class="text-sm font-extrabold text-slate-800 mt-0.5">{{ $item['value'] }}</p>
        </div>
        @endforeach
    </div>
</div>

<!-- ============ DAFTAR PESERTA ============ -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-5 md:p-6 border-b border-gray-100">
        <h3 class="text-sm font-extrabold text-slate-700 uppercase tracking-wider">Daftar Peserta ({{ $pesertas->count() }})</h3>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-slate-50 text-[#1a365d] text-xs uppercase tracking-wider border-b border-gray-100">
                    <th class="p-4 font-extrabold">Peserta</th>
                    <th class="p-4 font-extrabold">Instansi</th>
                    <th class="p-4 font-extrabold">Bukti Bayar</th>
                    <th class="p-4 font-extrabold text-center">Status Pendaftaran</th>
                    <th class="p-4 font-extrabold text-center">Status Pembayaran</th>
                    <th class="p-4 font-extrabold">Rincian Bayar</th>
                    <th class="p-4 font-extrabold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
                @forelse($pesertas as $p)
                <tr class="hover:bg-blue-50/40 border-b border-gray-50 align-top">
                    <!-- Peserta -->
                    <td class="p-4">
                        <div class="font-extrabold text-slate-800">{{ $p->nama_lengkap }}</div>
                        <div class="text-xs text-gray-400 mt-0.5">{{ $p->email_plataran_sehat }}</div>
                        <div class="text-xs text-gray-400">NIK: {{ $p->nik }}</div>
                    </td>

                    <!-- Instansi -->
                    <td class="p-4">
                        <div class="font-bold text-slate-700">{{ $p->instansi }}</div>
                        <div class="text-xs text-gray-400">{{ $p->departemen ?? '-' }}</div>
                    </td>

                    <!-- Bukti Bayar -->
                    <td class="p-4">
                        <div class="flex flex-col gap-1.5">
                            @if($p->bukti_bayar_pertama)
                            <a href="{{ asset('storage/'.$p->bukti_bayar_pertama) }}" target="_blank" class="text-xs font-bold text-blue-600 hover:underline flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Bukti Awal
                            </a>
                            @else
                            <span class="text-xs text-gray-400">Bukti awal: -</span>
                            @endif

                            @if($p->bukti_bayar_terakhir)
                            <a href="{{ asset('storage/'.$p->bukti_bayar_terakhir) }}" target="_blank" class="text-xs font-bold text-emerald-600 hover:underline flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Bukti Pelunasan
                            </a>
                            @endif
                        </div>
                    </td>

                    <!-- Status Pendaftaran -->
                    <td class="p-4 text-center">
                        @php
                            $badgePendaftaran = [
                                'Menunggu' => 'bg-amber-100 text-amber-700 border-amber-200',
                                'Diterima' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                'Ditolak' => 'bg-red-100 text-red-600 border-red-200',
                            ][$p->status_pendaftaran];
                        @endphp
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full border {{ $badgePendaftaran }}">{{ $p->status_pendaftaran }}</span>
                    </td>

                    <!-- Status Pembayaran -->
                    <td class="p-4 text-center">
                        @php
                            $badgeBayar = [
                                'Belum Bayar' => 'bg-slate-100 text-slate-500 border-slate-200',
                                'Cicil' => 'bg-orange-100 text-orange-700 border-orange-200',
                                'Lunas' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                            ][$p->status_pembayaran];
                        @endphp
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full border {{ $badgeBayar }}">{{ $p->status_pembayaran }}</span>
                    </td>

                    <!-- Rincian Bayar -->
                    <td class="p-4">
                        <div class="text-xs font-bold text-slate-700">Dibayar: Rp {{ number_format($p->total_dibayar, 0, ',', '.') }}</div>
                        @if($p->status_pembayaran === 'Cicil')
                        <div class="text-xs font-bold text-orange-600 mt-0.5">Kurang: Rp {{ number_format($p->kekurangan, 0, ',', '.') }}</div>
                        @endif
                    </td>

                    <!-- Aksi -->
                    <td class="p-4">
                        <div class="flex flex-col gap-1.5 items-stretch w-40">

                            @if($p->status_pendaftaran === 'Menunggu')
                                <button type="button" onclick="accPeserta('{{ $p->id }}', {{ $event->biaya_pelatihan }})" class="bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-2 rounded-lg text-xs font-bold shadow-sm">ACC Pendaftaran</button>
                                <button type="button" onclick="tolakPeserta('{{ $p->id }}', '{{ addslashes($p->nama) }}')" class="bg-red-50 hover:bg-red-500 hover:text-white text-red-600 px-3 py-2 rounded-lg text-xs font-bold border border-red-200">Tolak</button>

                            @elseif($p->status_pendaftaran === 'Diterima' && $p->status_pembayaran === 'Cicil')
                                <button type="button" onclick="copyLinkPelunasan('{{ $p->link_pembayaran }}')" class="bg-blue-50 hover:bg-blue-600 hover:text-white text-blue-700 px-3 py-2 rounded-lg text-xs font-bold border border-blue-200">Salin Link Pelunasan</button>
                                <button type="button" onclick="updateCicilan('{{ $p->id }}', {{ $event->biaya_pelatihan }}, {{ $p->kekurangan }})" class="bg-amber-50 hover:bg-amber-500 hover:text-white text-amber-700 px-3 py-2 rounded-lg text-xs font-bold border border-amber-200">Update Kekurangan</button>
                                <button type="button" onclick="tandaiLunas('{{ $p->id }}', '{{ addslashes($p->nama) }}')" class="bg-emerald-50 hover:bg-emerald-500 hover:text-white text-emerald-700 px-3 py-2 rounded-lg text-xs font-bold border border-emerald-200">Tandai Lunas</button>

                            @elseif($p->status_pendaftaran === 'Diterima' && $p->status_pembayaran === 'Lunas')
                                <span class="text-xs font-bold text-emerald-600 text-center py-2">✔ Selesai</span>

                            @elseif($p->status_pendaftaran === 'Ditolak')
                                <span class="text-xs font-bold text-gray-400 text-center py-2">Ditolak</span>
                            @endif
                        </div>

                        <!-- Hidden forms untuk setiap aksi -->
                        <form id="acc-form-{{ $p->id }}" action="{{ route('registrasi.acc', $p->id) }}" method="POST" class="hidden">
                            @csrf
                            <input type="hidden" name="keputusan" id="keputusan-{{ $p->id }}">
                            <input type="hidden" name="kekurangan" id="kekurangan-{{ $p->id }}">
                        </form>

                        <form id="tolak-form-{{ $p->id }}" action="{{ route('registrasi.tolak', $p->id) }}" method="POST" class="hidden">
                            @csrf
                        </form>

                        <form id="update-form-{{ $p->id }}" action="{{ route('registrasi.update-cicilan', $p->id) }}" method="POST" class="hidden">
                            @csrf
                            <input type="hidden" name="kekurangan" id="update-kekurangan-{{ $p->id }}">
                        </form>

                        <form id="lunas-form-{{ $p->id }}" action="{{ route('registrasi.lunas', $p->id) }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="p-10 text-center text-gray-500 font-bold">Belum ada peserta yang mendaftar.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
function accPeserta(id, biaya) {
    Swal.fire({
        title: 'Terima Pendaftaran Peserta',
        html: `<p class="text-sm text-gray-600">Pilih status pembayaran peserta ini:</p>`,
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: 'Lunas',
        denyButtonText: 'Cicil',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#059669',
        denyButtonColor: '#f59e0b',
        customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl px-6 py-2.5 font-bold', denyButton: 'rounded-xl px-6 py-2.5 font-bold', cancelButton: 'rounded-xl px-6 py-2.5 font-bold' }
    }).then((result) => {
        if (result.isConfirmed) {
            submitAcc(id, 'Lunas', null);
        } else if (result.isDenied) {
            Swal.fire({
                title: 'Sisa Kekurangan Pembayaran',
                html: `<p class="text-xs text-gray-500 mb-1">Biaya Pelatihan: <b>Rp ${Number(biaya).toLocaleString('id-ID')}</b></p>`,
                input: 'number',
                inputAttributes: { min: 0, max: biaya, step: 1000 },
                inputPlaceholder: 'Masukkan sisa kekurangan (Rp)',
                showCancelButton: true,
                confirmButtonText: 'Terima & Simpan',
                confirmButtonColor: '#1a365d',
                cancelButtonText: 'Batal',
                customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl px-6 py-2.5 font-bold', cancelButton: 'rounded-xl px-6 py-2.5 font-bold' },
                inputValidator: (value) => {
                    if (value === '' || value === null || value < 0) return 'Masukkan jumlah kekurangan yang valid';
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

function tolakPeserta(id, nama) {
    Swal.fire({
        title: 'Tolak Pendaftaran?',
        html: `Yakin ingin menolak pendaftaran <b>${nama}</b>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Tolak',
        confirmButtonColor: '#ef4444',
        cancelButtonText: 'Batal',
        customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl px-6 py-2.5 font-bold', cancelButton: 'rounded-xl px-6 py-2.5 font-bold' }
    }).then((result) => {
        if (result.isConfirmed) document.getElementById('tolak-form-' + id).submit();
    });
}

function updateCicilan(id, biaya, kekuranganLama) {
    Swal.fire({
        title: 'Update Sisa Kekurangan',
        html: `<p class="text-xs text-gray-500 mb-1">Biaya Pelatihan: <b>Rp ${Number(biaya).toLocaleString('id-ID')}</b></p>`,
        input: 'number',
        inputValue: kekuranganLama,
        inputAttributes: { min: 0, max: biaya, step: 1000 },
        showCancelButton: true,
        confirmButtonText: 'Simpan',
        confirmButtonColor: '#1a365d',
        cancelButtonText: 'Batal',
        customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl px-6 py-2.5 font-bold', cancelButton: 'rounded-xl px-6 py-2.5 font-bold' },
        inputValidator: (value) => {
            if (value === '' || value === null || value < 0) return 'Masukkan jumlah yang valid';
        }
    }).then((res) => {
        if (res.isConfirmed) {
            document.getElementById('update-kekurangan-' + id).value = res.value;
            document.getElementById('update-form-' + id).submit();
        }
    });
}

function tandaiLunas(id, nama) {
    Swal.fire({
        title: 'Tandai Lunas?',
        html: `Pembayaran <b>${nama}</b> akan ditandai LUNAS dan link pelunasan otomatis nonaktif.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Tandai Lunas',
        confirmButtonColor: '#059669',
        cancelButtonText: 'Batal',
        customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl px-6 py-2.5 font-bold', cancelButton: 'rounded-xl px-6 py-2.5 font-bold' }
    }).then((result) => {
        if (result.isConfirmed) document.getElementById('lunas-form-' + id).submit();
    });
}

function copyLinkPelunasan(link) {
    navigator.clipboard.writeText(link).then(() => {
        Swal.fire({ icon: 'success', title: 'Tersalin!', text: 'Link pelunasan disalin ke clipboard.', timer: 2000, showConfirmButton: false, toast: true, position: 'top-end', customClass: { popup: 'rounded-xl' } });
    });
}
</script>
@endsection