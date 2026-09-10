@extends('layouts.admin')
@section('title', 'Daftar Event')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-end gap-4">
    <div>
        <h2 class="text-3xl font-extrabold text-[#1a365d]">Manajemen Event</h2>
        <p class="text-gray-500 text-sm mt-1">Kelola daftar event pelatihan, rekap absensi/nilai, dan bagikan link akses publik.</p>
    </div>
    <a href="{{ route('event.create') }}" class="bg-[#1a365d] hover:bg-[#1ba1e2] text-white px-5 py-3 rounded-xl font-bold transition-all shadow-lg text-sm flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Buat Event Baru
    </a>
</div>

@if(session('success'))
<script>document.addEventListener('DOMContentLoaded', () => Swal.fire({icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", timer: 2000, showConfirmButton: false, customClass: {popup: 'rounded-2xl'}}));</script>
@endif

<!-- Search Bar -->
<div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6">
    <form action="{{ route('event.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center">
        <div class="relative flex-1 w-full">
            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}" class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#1ba1e2] text-sm font-medium" placeholder="Cari nama event, batch, atau master pelatihan...">
        </div>
        <div class="w-full md:w-auto flex gap-3">
            <button type="submit" class="bg-[#1ba1e2] hover:bg-blue-500 text-white px-6 py-3 rounded-xl font-bold shadow-md">Cari</button>
            @if(request('search'))
            <a href="{{ route('event.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-500 px-4 py-3 rounded-xl font-bold flex items-center">Reset</a>
            @endif
        </div>
    </form>
</div>

<!-- Tabel Data -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto pb-4">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-slate-50 text-[#1a365d] text-sm uppercase tracking-wider border-b border-gray-100">
                    <th class="p-5 font-extrabold">Nama Event / Pelatihan</th>
                    <th class="p-5 font-extrabold">Jadwal & Sistem</th>
                    <th class="p-5 font-extrabold">Info / Biaya</th>
                    <th class="p-5 font-extrabold text-center">Data Master</th>
                    <th class="p-5 font-extrabold text-center">Tindakan & Share Link</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                @forelse($events as $item)
                
                @php
                    // 1. Data Fasilitator Unik (Untuk peserta menilai Fasilitator, tetap per orang)
                    $fasilitatorUnik = $item->eventFasilitatorMateris ? $item->eventFasilitatorMateris->pluck('fasilitator')->filter()->unique('id')->map(function($f) {
                        return ['id' => $f->id, 'nama' => $f->nama_fasilitator];
                    })->values()->toJson() : '[]';

                    // 2. Data Fasilitator + Materi (Untuk Fasilitator menilai Peserta per materi yang diajarkan)
                    $fasilMateriList = $item->eventFasilitatorMateris ? $item->eventFasilitatorMateris->map(function($efm) {
                        return [
                            'fasilitator_id' => $efm->fasilitator_id,
                            'fasilitator_nama' => $efm->fasilitator->nama_fasilitator ?? 'Tidak Diketahui',
                            'materi_id' => $efm->evaluasi_materi_id,
                            'materi_nama' => $efm->evaluasiMateri->nama_materi ?? 'Tidak Diketahui'
                        ];
                    })->values()->toJson() : '[]';
                @endphp

                <tr class="hover:bg-blue-50/50 border-b border-gray-50 transition-colors">
                    <!-- Kolom 1: Nama Event -->
                    <td class="p-5">
                        <div class="font-extrabold text-[#1a365d] text-base">{{ $item->nama_event }}</div>
                        <div class="text-xs text-gray-500 font-bold mt-1">Master: {{ $item->pelatihan->nama_pelatihan ?? '-' }}</div>
                        <div class="text-[11px] text-gray-400 mt-0.5">Batch {{ $item->batch ?? '-' }} | Thn {{ $item->tahun }} | {{ $item->tipe_pelatihan }}</div>
                    </td>
                    
                    <!-- Kolom 2: Jadwal & Sistem -->
                    <td class="p-5">
                        <div class="text-sm font-bold text-gray-800">{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}</div>
                        <div class="mt-1 flex gap-2">
                            <span class="bg-indigo-100 text-indigo-700 text-xs font-bold px-2 py-0.5 rounded border border-indigo-200">{{ $item->sistem_pelatihan }}</span>
                            <span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-0.5 rounded border border-green-200">{{ $item->jenis_pelatihan }}</span>
                        </div>
                    </td>
                    
                    <!-- Kolom 3: Sertifikat & Biaya -->
                    <td class="p-5">
                        <div class="text-xs font-bold text-gray-600">SKP: <span class="text-blue-600">{{ $item->skp }}</span></div>
                        <div class="text-xs font-bold text-gray-600 mt-1">Biaya: Rp {{ number_format($item->biaya_pelatihan, 0, ',', '.') }}</div>
                    </td>
                    
                    <!-- Kolom 4: Kumpulan Tombol Rekap -->
                    <td class="p-5 text-center">
                        <div class="flex flex-col gap-1.5 items-center">
                            <button onclick="Swal.fire('Segera Hadir', 'Halaman Rekap Absensi sedang dikerjakan', 'info')" class="flex items-center justify-center gap-1.5 bg-teal-50 text-teal-600 hover:bg-teal-500 hover:text-white px-3 py-1.5 rounded-lg border border-teal-200 text-xs font-bold transition-all w-28">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg> Absensi
                            </button>
<a href="{{ route('event.pembayaran', $item->id) }}" class="flex items-center justify-center gap-1.5 bg-orange-50 text-orange-600 hover:bg-orange-500 hover:text-white px-3 py-1.5 rounded-lg border border-orange-200 text-xs font-bold transition-all w-28">
    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Pembayaran
</a>
                            <button onclick="Swal.fire('Segera Hadir', 'Halaman Akumulasi Nilai sedang dikerjakan', 'info')" class="flex items-center justify-center gap-1.5 bg-purple-50 text-purple-600 hover:bg-purple-500 hover:text-white px-3 py-1.5 rounded-lg border border-purple-200 text-xs font-bold transition-all w-28">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg> Nilai
                            </button>
                        </div>
                    </td>

                    <!-- Kolom 5: Aksi & Links -->
                    <td class="p-5">
                        <div class="flex items-center gap-2 mb-2 justify-center">
                            <button onclick="showLinkGeneral('{{ $item->uuid }}')" class="flex items-center gap-1.5 bg-blue-100 text-blue-700 hover:bg-blue-600 hover:text-white px-3 py-2 rounded-lg text-xs font-bold transition-all shadow-sm border border-blue-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg> Link Umum
                            </button>
                            <button onclick="showLinkPenilaian('{{ $item->uuid }}', {{ $fasilitatorUnik }}, {{ $fasilMateriList }})" class="flex items-center gap-1.5 bg-indigo-100 text-indigo-700 hover:bg-indigo-600 hover:text-white px-3 py-2 rounded-lg text-xs font-bold transition-all shadow-sm border border-indigo-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg> Penilaian
                            </button>
                        </div>
                        
                        <div class="flex items-center justify-center gap-2">
                            <button onclick='copyWaBlast(@json($item), {{ $fasilitatorUnik }}, {{ $fasilMateriList }})' class="flex items-center gap-1.5 bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-2 rounded-lg text-xs font-bold transition-all shadow-md">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg> Copy WA Blast
                            </button>
                            
                            <a href="{{ route('event.edit', $item->id) }}" class="bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white p-2 rounded-lg border border-amber-200 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></a>
                            <form action="{{ route('event.destroy', $item->id) }}" method="POST" class="inline-block">
                                @csrf @method('DELETE')
                                <button type="button" onclick="confirmDeleteEvent('{{ $item->id }}', '{{ addslashes($item->nama_event) }}')" class="bg-red-50 text-red-500 hover:bg-red-500 hover:text-white p-2 rounded-lg border border-red-200 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="p-10 text-center text-gray-500 font-bold">Belum ada Event yang dibuat.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($events->hasPages()) <div class="p-5 border-t border-gray-100 bg-gray-50">{{ $events->links() }}</div> @endif
</div>

<!-- KUMPULAN JAVASCRIPT -->
<script>
    const baseUrl = "{{ url('/') }}";

    function confirmDeleteEvent(id, name) {
        Swal.fire({
            title: 'Hapus Event?',
            html: `Yakin ingin menghapus <b>${name}</b>?<br><span class="text-red-500 text-sm">Semua data pemetaan materi dan fasilitator akan ikut terhapus!</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl px-6 py-2.5 font-bold', cancelButton: 'rounded-xl px-6 py-2.5 font-bold' }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }

    function showLinkGeneral(uuid) {
        Swal.fire({
            title: '<span class="text-[#1a365d] font-extrabold">Link Publik Event</span>',
            html: `
                <div class="text-left space-y-4 mt-4">
                    <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 shadow-inner">
                        <p class="text-xs font-bold text-blue-800 mb-2">Registrasi Peserta</p>
                        <input type="text" readonly value="${baseUrl}/registrasi/${uuid}" class="w-full text-sm p-3 border border-blue-200 rounded-lg bg-white text-gray-700 font-medium cursor-text" onclick="this.select()">
                    </div>
                    <div class="bg-emerald-50 p-4 rounded-xl border border-emerald-100 shadow-inner">
                        <p class="text-xs font-bold text-emerald-800 mb-2">Presensi Kehadiran</p>
                        <input type="text" readonly value="${baseUrl}/presensi/${uuid}" class="w-full text-sm p-3 border border-emerald-200 rounded-lg bg-white text-gray-700 font-medium cursor-text" onclick="this.select()">
                    </div>
                </div>
            `,
            confirmButtonText: 'Tutup',
            confirmButtonColor: '#1a365d',
            customClass: { popup: 'rounded-3xl', confirmButton: 'rounded-xl px-8 py-3 font-bold' }
        });
    }

    function showLinkPenilaian(uuid, fasilitatorUnik, fasilMateris) {
        let html = `<div class="text-left space-y-4 mt-2 max-h-[65vh] overflow-y-auto pr-2 custom-scrollbar">`;

        // 1. Peserta -> Pelatihan
        html += `
            <div class="bg-indigo-50 p-4 rounded-xl border border-indigo-200 shadow-sm">
                <p class="text-xs font-bold text-indigo-800 mb-2">Link Penilaian Pelatihan (Wajib Peserta)</p>
                <input type="text" readonly value="${baseUrl}/evaluasi-pelatihan/${uuid}" class="w-full text-sm p-3 border border-indigo-200 rounded-lg bg-white text-gray-700 font-medium cursor-text" onclick="this.select()">
            </div>
            
            <div class="border-t border-gray-200 pt-3 mt-4 mb-2">
                <p class="font-extrabold text-sm text-gray-800">Evaluasi Peserta ➔ Fasilitator</p>
                <p class="text-[11px] text-gray-500 mb-3">Link evaluasi spesifik berdasarkan nama pengajar.</p>
            </div>
        `;

        // 2. Peserta -> Fasilitator (Per Orang)
        if (fasilitatorUnik.length > 0) {
            fasilitatorUnik.forEach(f => {
                html += `
                    <div class="bg-white p-3 rounded-xl border border-gray-200 mb-3 shadow-sm hover:border-blue-300 transition-colors">
                        <p class="text-xs font-bold text-gray-800 mb-1.5">👤 ${f.nama}</p>
                        <input type="text" readonly value="${baseUrl}/evaluasi-fasilitator/${uuid}/${f.id}" class="w-full text-[13px] p-2.5 border border-gray-200 rounded-lg bg-gray-50 focus:outline-none focus:bg-blue-50 focus:border-blue-300 text-gray-600 font-medium cursor-text" onclick="this.select()">
                    </div>
                `;
            });
        } else {
            html += `<p class="text-xs text-red-500 font-bold p-3 bg-red-50 rounded-xl border border-red-200">Belum ada pengajar.</p>`;
        }

        // 3. Fasilitator -> Peserta (Per Orang dan Per Materi)
        html += `
            <div class="border-t border-gray-200 pt-3 mt-4 mb-2">
                <p class="font-extrabold text-sm text-gray-800">Akses Khusus: Fasilitator ➔ Menilai Peserta</p>
                <p class="text-[11px] text-gray-500 mb-3">Link khusus untuk fasilitator memberikan nilai berdasarkan materi yang diajarkan.</p>
            </div>
        `;

        if (fasilMateris.length > 0) {
            fasilMateris.forEach(fm => {
                html += `
                    <div class="bg-amber-50 p-3 rounded-xl border border-amber-200 mb-3 shadow-sm">
                        <p class="text-xs font-bold text-amber-900 mb-0.5">👨‍🏫 ${fm.fasilitator_nama}</p>
                        <p class="text-[11px] font-semibold text-amber-700 mb-2">📚 Materi: ${fm.materi_nama}</p>
                        <input type="text" readonly value="${baseUrl}/penilaian-skill/${uuid}/${fm.fasilitator_id}/${fm.materi_id}" class="w-full text-[13px] p-2.5 border border-amber-200 rounded-lg bg-white focus:outline-none text-gray-700 font-medium cursor-text" onclick="this.select()">
                    </div>
                `;
            });
        } else {
            html += `<p class="text-xs text-red-500 font-bold p-3 bg-red-50 rounded-xl border border-red-200">Belum ada fasilitator yang mengampu materi.</p>`;
        }

        html += `</div>`;

        Swal.fire({
            title: '<span class="text-[#1a365d] font-extrabold">Link Penilaian Event</span>',
            html: html,
            confirmButtonText: 'Tutup',
            confirmButtonColor: '#4f46e5',
            customClass: { popup: 'rounded-3xl', confirmButton: 'rounded-xl px-8 py-3 font-bold' }
        });
    }

    // Tombol Copy Template WhatsApp Blast Terpisah (Satu Kali Klik)
    function copyWaBlast(event, fasilitatorUnik, fasilMateris) {
        const tglMulai = new Date(event.tanggal_mulai).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
        const tglSelesai = new Date(event.tanggal_selesai).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
        
        // Bagian Untuk Peserta
        let text = `📢 *INFORMASI AKSES PELATIHAN*\n\n`;
        text += `*${event.nama_event.toUpperCase()}*\n`;
        text += `🗓️ Jadwal: ${tglMulai} s/d ${tglSelesai}\n`;
        text += `📍 Sistem: ${event.sistem_pelatihan} ${event.lokasi ? '('+event.lokasi+')' : ''}\n\n`;
        
        text += `Berikut adalah kumpulan link akses untuk *PESERTA*:\n\n`;
        text += `📝 *1. Registrasi Peserta:*\n${baseUrl}/registrasi/${event.uuid}\n\n`;
        text += `✅ *2. Presensi Kehadiran:*\n${baseUrl}/presensi/${event.uuid}\n\n`;
        text += `⭐ *3. Evaluasi Penyelenggaraan (Wajib):*\n${baseUrl}/evaluasi-pelatihan/${event.uuid}\n\n`;
        
        text += `👤 *4. Evaluasi Fasilitator:*\n`;
        if (fasilitatorUnik.length > 0) {
            fasilitatorUnik.forEach(f => {
                text += `▪️ ${f.nama}:\n${baseUrl}/evaluasi-fasilitator/${event.uuid}/${f.id}\n\n`;
            });
        } else {
            text += `(Belum ada fasilitator)\n\n`;
        }

        // Bagian Khusus Untuk Fasilitator
        text += `-------------------------------------------\n`;
        text += `👨‍🏫 *KHUSUS FASILITATOR (Link Menilai Peserta)*\n`;
        text += `-------------------------------------------\n`;
        if (fasilMateris.length > 0) {
            fasilMateris.forEach(fm => {
                text += `▪️ ${fm.fasilitator_nama}\n(Materi: ${fm.materi_nama})\n${baseUrl}/penilaian-skill/${event.uuid}/${fm.fasilitator_id}/${fm.materi_id}\n\n`;
            });
        } else {
            text += `(Belum ada data materi/fasilitator)\n\n`;
        }
        
        text += `Terima kasih atas partisipasi Anda. Semangat! ✨`;

        navigator.clipboard.writeText(text).then(() => {
            Swal.fire({
                icon: 'success', title: 'Tersalin!', text: 'Template broadcast WhatsApp telah disalin ke clipboard.',
                timer: 2500, showConfirmButton: false, toast: true, position: 'top-end', customClass: { popup: 'rounded-xl' }
            });
        }).catch(err => {
            Swal.fire('Gagal!', 'Gagal menyalin text.', 'error');
        });
    }
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
@endsection