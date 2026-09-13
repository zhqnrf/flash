@extends('layouts.admin')
@section('title', 'Manajemen Pengaduan (SIMPEL)')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-end gap-4">
    <div>
        <h2 class="text-3xl font-extrabold text-[#1a365d]">Pengaduan Pelatihan</h2>
        <p class="text-gray-500 text-sm mt-1">Kelola aspirasi, kritik, dan pengaduan layanan pelatihan dari peserta/masyarakat.</p>
    </div>
</div>

@if(session('success'))
<script>document.addEventListener('DOMContentLoaded', () => Swal.fire({icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", timer: 2000, showConfirmButton: false, customClass: {popup: 'rounded-2xl'}}));</script>
@endif

<!-- Search & Filter Bar -->
<div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6">
    <form action="{{ route('pengaduan.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center">
        <!-- Search Input -->
        <div class="relative flex-1 w-full">
            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}" class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#1ba1e2] text-sm font-medium" placeholder="Cari nama, email, tempat kejadian, atau isi pengaduan...">
        </div>

        <!-- Filter Status -->
        <div class="w-full md:w-48">
            <select name="status" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#1ba1e2] text-sm font-medium text-gray-700">
                <option value="">Semua Status</option>
                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Diproses" {{ request('status') == 'Diproses' ? 'selected' : '' }}>Diproses</option>
                <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
            </select>
        </div>

        <!-- Tombol Aksi Filter -->
        <div class="w-full md:w-auto flex gap-3">
            <button type="submit" class="bg-[#1ba1e2] hover:bg-blue-500 text-white px-6 py-3 rounded-xl font-bold shadow-md">Filter</button>
            <div class="flex justify-end gap-3 mb-4">
    <a href="{{ route('pengaduan.cetak-pdf', ['status' => request('status')]) }}" target="_blank" class="bg-rose-600 hover:bg-rose-700 text-white px-4 py-2.5 rounded-xl font-bold text-xs shadow flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg> Cetak PDF Laporan
    </a>
    <a href="{{ route('pengaduan.export-excel') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-xl font-bold text-xs shadow flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg> Export Excel
    </a>
</div>
            @if(request('search') || request('status'))
            <a href="{{ route('pengaduan.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-500 px-4 py-3 rounded-xl font-bold flex items-center">Reset</a>
            @endif
        </div>
    </form>
</div>

<!-- Tabel Data Pengaduan -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto pb-4">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-slate-50 text-[#1a365d] text-sm uppercase tracking-wider border-b border-gray-100">
                    <th class="p-5 font-extrabold">Pelapor</th>
                    <th class="p-5 font-extrabold">Kejadian & Pengaduan</th>
                    <th class="p-5 font-extrabold">Kritik & Saran</th>
                    <th class="p-5 font-extrabold text-center">Status</th>
                    <th class="p-5 font-extrabold text-center">Aksi & Balasan WA</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                @forelse($pengaduans as $item)
                <tr class="hover:bg-blue-50/50 border-b border-gray-50 transition-colors">
                    <!-- Pelapor -->
                    <td class="p-5">
                        <div class="font-extrabold text-[#1a365d] text-base">{{ $item->nama_lengkap }}</div>
                        <div class="text-xs text-gray-500 font-medium mt-0.5"> {{ $item->email }}</div>
                        <div class="text-xs text-emerald-600 font-bold mt-0.5">{{ $item->no_whatsapp }}</div>
                    </td>
                    
                    <!-- Kejadian & Pengaduan -->
                    <td class="p-5 max-w-xs whitespace-normal">
                        <div class="text-xs font-bold text-blue-600 uppercase">📍 {{ $item->tempat_kejadian }}</div>
                        <p class="text-sm text-gray-700 mt-1 line-clamp-2">{{ $item->isi_pengaduan }}</p>
                        <span class="text-[10px] text-gray-400 mt-1 block">{{ $item->created_at->format('d M Y, H:i') }}</span>
                    </td>

                    <!-- Kritik & Saran -->
                    <td class="p-5 max-w-xs whitespace-normal">
                        <p class="text-xs text-gray-600 italic">{{ $item->kritik_saran ?? '-' }}</p>
                    </td>

                    <!-- Status -->
                    <td class="p-5 text-center">
                        @if($item->status == 'Pending')
                            <span class="bg-amber-100 text-amber-700 text-xs font-bold px-3 py-1 rounded-full border border-amber-200">Pending</span>
                        @elseif($item->status == 'Diproses')
                            <span class="bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full border border-blue-200">Diproses</span>
                        @else
                            <span class="bg-emerald-100 text-emerald-700 text-xs font-bold px-3 py-1 rounded-full border border-emerald-200">Selesai</span>
                        @endif
                    </td>

                    <!-- Aksi -->
                    <td class="p-5 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <!-- Tombol Beri/Edit Tanggapan -->
                            <button onclick="bukaModalJawab('{{ $item->id }}', '{{ addslashes($item->nama_lengkap) }}', '{{ $item->status }}', `{{ addslashes($item->jawaban_admin ?? '') }}`)" class="bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white px-3 py-2 rounded-lg text-xs font-bold transition-all shadow-sm border border-indigo-200 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg> Tanggapi
                            </button>

                            <!-- Tombol Kirim WA Langsung -->
                            <button onclick="kirimWa('{{ $item->no_whatsapp }}', '{{ addslashes($item->nama_lengkap) }}', `{{ addslashes($item->jawaban_admin ?? 'Belum ada tanggapan') }}`)" class="bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-2 rounded-lg text-xs font-bold transition-all shadow-md flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg> Kirim WA
                            </button>
<a href="{{ route('pengaduan.cetak-detail', $item->id) }}" target="_blank" class="bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white px-3 py-2 rounded-lg text-xs font-bold transition-all shadow-sm border border-rose-200 flex items-center gap-1 title="Cetak Laporan ini">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg> Cetak
        </a>
                            <!-- Hapus -->
                            <form action="{{ route('pengaduan.destroy', $item->id) }}" method="POST" class="inline-block delete-form">
                                @csrf @method('DELETE')
                                <button type="button" onclick="konfirmasiHapus(this)" class="bg-red-50 text-red-500 hover:bg-red-500 hover:text-white p-2 rounded-lg border border-red-200 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="p-10 text-center text-gray-500 font-bold">Belum ada data pengaduan masuk.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($pengaduans->hasPages()) <div class="p-5 border-t border-gray-100 bg-gray-50">{{ $pengaduans->links() }}</div> @endif
</div>

<!-- Modal Tanggapan Admin -->
<div id="modalTanggapan" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-gray-100">
        <h3 class="text-xl font-extrabold text-[#1a365d] mb-2">Tanggapan Pengaduan</h3>
        <p class="text-xs text-gray-500 mb-4">Balas dan perbarui status penanganan laporan dari <span id="modalNama" class="font-bold text-gray-800"></span></p>

        <form id="formTanggapan" method="POST">
            @csrf @method('PUT')
            
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Status Penanganan</label>
                <select name="status" id="modalStatus" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold focus:outline-none focus:border-[#1ba1e2]">
                    <option value="Pending">Pending</option>
                    <option value="Diproses">Diproses</option>
                    <option value="Selesai">Selesai</option>
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Isi Jawaban / Solusi Admin</label>
                <textarea name="jawaban_admin" id="modalJawaban" rows="4" required placeholder="Tuliskan tanggapan resmi dari instansi..." class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#1ba1e2]"></textarea>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="tutupModalJawab()" class="px-5 py-2.5 rounded-xl bg-gray-100 text-gray-600 font-bold text-sm">Batal</button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#1a365d] hover:bg-[#1ba1e2] text-white font-bold text-sm shadow">Simpan Tanggapan</button>
            </div>
        </form>
    </div>
</div>

<script>
function bukaModalJawab(id, nama, status, jawaban) {
    document.getElementById('modalNama').innerText = nama;
    document.getElementById('modalStatus').value = status;
    document.getElementById('modalJawaban').value = jawaban === 'null' ? '' : jawaban;
    
    // UBAH BAGIAN INI: Tambahkan 'admin/pengaduan'
    document.getElementById('formTanggapan').action = "{{ url('admin/pengaduan') }}/" + id;
    
    document.getElementById('modalTanggapan').classList.remove('hidden');
}

    function kirimWa(noWa, nama, jawaban) {
        let nomor = noWa.startsWith('0') ? '62' + noWa.slice(1) : noWa;
        let pesan = `Halo Bapak/Ibu *${nama}*,\n\nTerima kasih telah menyampaikan pengaduan/masukan melalui FIT ONE.\n\nTanggapan dari Tim Admin:\n_${jawaban}_\n\nSalam hangat,\n*Tim Pelayanan FIT-C*`;
        
        let url = `https://wa.me/${nomor}?text=${encodeURIComponent(pesan)}`;
        window.open(url, '_blank');
    }

    function konfirmasiHapus(button) {
        Swal.fire({
            title: 'Hapus Pengaduan?',
            text: 'Data laporan ini akan dihapus permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-2xl' }
        }).then((result) => {
            if (result.isConfirmed) {
                button.closest('form').submit();
            }
        });
    }
</script>
@endsection