@extends('layouts.admin')
@section('title', 'Master Fasilitator')

@section('content')
<!-- CDN SheetJS (xlsx) untuk Excel Handler -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<!-- CDN SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Header Halaman -->
<div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-end gap-4">
    <div>
        <h2 class="text-2xl md:text-3xl font-extrabold text-[#1a365d]">Master Fasilitator</h2>
        <p class="text-gray-500 text-sm mt-1">Kelola data pengajar, profesi, dokumen pendukung, foto 3x4, dan materi yang diampu.</p>
    </div>
    
    <!-- Tombol Aksi Excel & Tambah -->
    <div class="flex flex-wrap gap-2 items-center">
        <!-- Download Template Excel -->
        <button onclick="downloadTemplateFasilitator()" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-3 rounded-xl font-bold transition-all shadow-md text-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            Template Excel
        </button>

        <!-- Import Excel (Backend) -->
        <label class="bg-purple-600 hover:bg-purple-500 text-white px-4 py-3 rounded-xl font-bold transition-all shadow-md text-sm cursor-pointer flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
            Import Excel
            <input type="file" id="import_file" accept=".xlsx, .xls, .csv" class="hidden" onchange="handleImportFasilitator(event)">
        </label>

        <!-- Export Excel (Backend Full Data) -->
        <button onclick="exportExcelFasilitator()" class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-3 rounded-xl font-bold transition-all shadow-md text-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Export Excel
        </button>

        <!-- Tambah Data -->
        <a href="{{ route('master.fasilitator.create') }}" class="bg-[#1a365d] hover:bg-[#1ba1e2] text-white px-5 py-3 rounded-xl font-bold transition-all shadow-lg text-sm whitespace-nowrap">
            + Tambah Fasilitator
        </a>
    </div>
</div>

<!-- Notifikasi Sukses dari Backend via SweetAlert -->
@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            timer: 2000,
            showConfirmButton: false,
            customClass: { popup: 'rounded-2xl' }
        });
    });
</script>
@endif

<!-- Baris Pencarian (Search) -->
<div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6">
    <form action="{{ route('master.fasilitator.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center">
        <div class="relative flex-1 w-full">
            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}" class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#1ba1e2] text-sm font-medium" placeholder="Cari nama fasilitator, profesi, atau tempat kerja...">
        </div>
        <div class="w-full md:w-auto flex gap-3">
            <button type="submit" class="bg-[#1ba1e2] hover:bg-blue-500 text-white px-6 py-3 rounded-xl font-bold shadow-md">Cari</button>
            @if(request('search'))
            <a href="{{ route('master.fasilitator.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-500 px-4 py-3 rounded-xl font-bold flex items-center">Reset</a>
            @endif
        </div>
    </form>
</div>

<!-- Container Tabel Data -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-slate-50 text-[#1a365d] text-sm uppercase tracking-wider border-b border-gray-100">
                    <th class="p-5 font-extrabold w-16 text-center">No</th>
                    <th class="p-5 font-extrabold">Foto & Nama</th>
                    <th class="p-5 font-extrabold">Profesi & Jabatan</th>
                    <th class="p-5 font-extrabold">Tempat Kerja</th>
                    <th class="p-5 font-extrabold">Materi Yang Diampu</th>
                    <th class="p-5 font-extrabold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                @forelse($fasilitators as $index => $item)
                <tr class="hover:bg-blue-50/50 border-b border-gray-50 transition-colors">
                    <td class="p-5 text-center font-semibold text-gray-400">
                        {{ $fasilitators->firstItem() + $index }}
                    </td>
                    <td class="p-5 flex items-center gap-3">
                        @if($item->foto_fasilitator)
                            <img src="{{ asset('storage/' . $item->foto_fasilitator) }}" class="w-10 h-12 object-cover rounded-lg shadow-sm border">
                        @else
                            <div class="w-10 h-12 bg-gray-200 rounded-lg flex items-center justify-center text-xs text-gray-500 font-bold">3x4</div>
                        @endif
                        <div>
                            <div class="font-bold text-[#1a365d]">{{ $item->nama_fasilitator }}</div>
                            <div class="text-xs text-gray-400">{{ $item->pendidikan_terakhir_fasilitator }}</div>
                        </div>
                    </td>
                    <td class="p-5">
                        <div class="font-semibold text-gray-800">{{ $item->profesi_fasilitator ?? '-' }}</div>
                        <div class="text-xs text-gray-400">{{ $item->jabatan_fasilitator }}</div>
                    </td>
                    <td class="p-5 font-medium text-gray-600">{{ $item->tempat_kerja_fasilitator ?? '-' }}</td>
                    <td class="p-5">
                        <div class="flex flex-wrap gap-1 max-w-xs">
                            @foreach($item->materis as $m)
                                <span class="bg-cyan-50 text-cyan-700 text-xs font-bold px-2.5 py-0.5 rounded border border-cyan-100">{{ $m->nama_materi }}</span>
                            @endforeach
                        </div>
                    </td>
                    <td class="p-5 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <!-- Tombol Detail (Modal) -->
                            <button onclick="openDetailModal({{ json_encode($item) }}, {{ json_encode($item->materis) }})" class="bg-blue-50 text-blue-600 hover:bg-blue-500 hover:text-white p-2 rounded-lg transition-colors border border-blue-200" title="Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </button>

                            <!-- Tombol Edit -->
                            <a href="{{ route('master.fasilitator.edit', $item->id) }}" class="bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white p-2 rounded-lg transition-colors border border-amber-200" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </a>
                            
                            <!-- Tombol Hapus dengan SweetAlert Confirm -->
                            <form id="delete-form-{{ $item->id }}" action="{{ route('master.fasilitator.destroy', $item->id) }}" method="POST" class="inline-block">
                                @csrf @method('DELETE')
                                <button type="button" onclick="confirmDeleteFasilitator('{{ $item->id }}', '{{ addslashes($item->nama_fasilitator) }}')" class="bg-red-50 text-red-500 hover:bg-red-500 hover:text-white p-2 rounded-lg transition-colors border border-red-200" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-10 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <p class="text-gray-500 font-bold text-lg">Data Tidak Ditemukan</p>
                            <p class="text-gray-400 text-sm mt-1">Belum ada data fasilitator yang tersedia.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginasi -->
    @if($fasilitators->hasPages())
    <div class="p-5 border-t border-gray-100 bg-gray-50">
        {{ $fasilitators->links() }}
    </div>
    @endif
</div>

<!-- Modal Detail Fasilitator -->
<div id="detailModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full p-6 md:p-8 shadow-2xl relative max-h-[90vh] overflow-y-auto">
        <button onclick="closeDetailModal()" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 font-bold text-xl">✕</button>
        
        <div class="flex items-start gap-4 pb-6 border-b border-gray-100">
            <div id="modal-foto-container" class="w-20 h-24 bg-gray-100 rounded-xl overflow-hidden border shrink-0 flex items-center justify-center">
                <!-- Foto preview di modal -->
            </div>
            <div>
                <h3 id="modal-nama" class="text-xl font-extrabold text-[#1a365d]"></h3>
                <p id="modal-profesi" class="text-sm text-gray-500 font-medium"></p>
                <p id="modal-tempat-kerja" class="text-xs text-gray-400 mt-1"></p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 py-6 border-b border-gray-100 text-sm">
            <div>
                <span class="text-gray-400 block font-medium">Jabatan</span>
                <span id="modal-jabatan" class="font-bold text-gray-800"></span>
            </div>
            <div>
                <span class="text-gray-400 block font-medium">Pendidikan Terakhir</span>
                <span id="modal-pendidikan" class="font-bold text-gray-800"></span>
            </div>
            <div class="md:col-span-2">
                <span class="text-gray-400 block font-medium mb-1">Dokumen Pendukung</span>
                <div id="modal-dokumen"></div>
            </div>
        </div>

        <div class="pt-6">
            <h4 class="font-bold text-sm text-gray-700 mb-3">Materi Yang Diampu:</h4>
            <div id="modal-materi-list" class="flex flex-wrap gap-2">
                <!-- List materi -->
            </div>
        </div>
    </div>
</div>

<!-- Script SweetAlert & Excel Handler -->
<script>
    function openDetailModal(fasilitator, materis) {
        document.getElementById('modal-nama').innerText = fasilitator.nama_fasilitator;
        document.getElementById('modal-profesi').innerText = fasilitator.profesi_fasilitator || '-';
        document.getElementById('modal-tempat-kerja').innerText = fasilitator.tempat_kerja_fasilitator || '-';
        document.getElementById('modal-jabatan').innerText = fasilitator.jabatan_fasilitator || '-';
        document.getElementById('modal-pendidikan').innerText = fasilitator.pendidikan_terakhir_fasilitator || '-';

        // Foto
        const fotoContainer = document.getElementById('modal-foto-container');
        if (fasilitator.foto_fasilitator) {
            fotoContainer.innerHTML = `<img src="/storage/${fasilitator.foto_fasilitator}" class="w-full h-full object-cover">`;
        } else {
            fotoContainer.innerHTML = `<span class="text-xs text-gray-400 font-bold">3x4</span>`;
        }

        // Dokumen
        const dokContainer = document.getElementById('modal-dokumen');
        if (fasilitator.dokumen_fasilitator) {
            dokContainer.innerHTML = `<a href="/storage/${fasilitator.dokumen_fasilitator}" target="_blank" class="inline-block bg-emerald-50 text-emerald-600 px-4 py-2 rounded-xl text-xs font-bold border border-emerald-200">Lihat / Download Dokumen PDF</a>`;
        } else {
            dokContainer.innerHTML = `<span class="text-xs text-gray-400 italic">Tidak ada dokumen diunggah</span>`;
        }

        // Materi
        const materiList = document.getElementById('modal-materi-list');
        materiList.innerHTML = '';
        if (materis.length > 0) {
            materis.forEach(m => {
                materiList.innerHTML += `<span class="bg-cyan-50 text-cyan-700 text-xs font-bold px-3 py-1.5 rounded-xl border border-cyan-100">${m.nama_materi}</span>`;
            });
        } else {
            materiList.innerHTML = `<span class="text-xs text-gray-400 italic">Belum ada materi yang diampu</span>`;
        }

        document.getElementById('detailModal').classList.remove('hidden');
    }

    function closeDetailModal() {
        document.getElementById('detailModal').classList.add('hidden');
    }

    // SweetAlert Konfirmasi Hapus
    function confirmDeleteFasilitator(id, name) {
        Swal.fire({
            title: 'Hapus Fasilitator?',
            html: "Yakin ingin menghapus data <b>" + name + "</b>?",
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

    // 1. Download Template Excel Fasilitator
    function downloadTemplateFasilitator() {
        const data = [{ 
            "Nama Fasilitator": "Dr. Contoh, Sp.An", 
            "Profesi": "Dokter Spesialis", 
            "Jabatan": "Widyaiswara", 
            "Tempat Kerja": "RSUD Simpang Lima Gumul Kediri", 
            "Pendidikan Terakhir": "S2 Kedokteran", 
            "Materi Yang Diampu (Pisahkan koma)": "Contoh Materi A, Contoh Materi B" 
        }];
        const ws = XLSX.utils.json_to_sheet(data);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Template Fasilitator");
        XLSX.writeFile(wb, "Template_Master_Fasilitator.xlsx");
    }

    // 2. Export Seluruh Data dari Backend via SheetJS
    function exportExcelFasilitator() {
        Swal.fire({ title: 'Menyiapkan seluruh data...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

        fetch("{{ route('master.fasilitator.export') }}")
            .then(res => res.json())
            .then(data => {
                Swal.close();
                if (data.length === 0) {
                    Swal.fire('Info', 'Tidak ada data untuk diexport.', 'info');
                    return;
                }
                const ws = XLSX.utils.json_to_sheet(data);
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, "Data Fasilitator");
                XLSX.writeFile(wb, "Semua_Data_Master_Fasilitator.xlsx");
            })
            .catch(err => {
                Swal.fire('Error', 'Gagal mengambil data dari server.', 'error');
            });
    }

    // 3. Import Excel ke Backend dengan SweetAlert
    function handleImportFasilitator(event) {
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            const data = new Uint8Array(e.target.result);
            const workbook = XLSX.read(data, { type: 'array' });
            const json = XLSX.utils.sheet_to_json(workbook.Sheets[workbook.SheetNames[0]]);
            
            if (json.length === 0) {
                Swal.fire('Gagal', 'File Excel kosong atau format tidak sesuai!', 'error');
                return;
            }

            Swal.fire({
                title: 'Konfirmasi Import Backend',
                text: `Akan memproses ${json.length} baris data ke server. Lanjutkan?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Proses Import!',
                cancelButtonText: 'Batal',
                customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl px-6 py-2.5 font-bold', cancelButton: 'rounded-xl px-6 py-2.5 font-bold' }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Mengimpor ke database...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

                    fetch("{{ route('master.fasilitator.import') }}", {
                        method: "POST",
                        headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                        body: JSON.stringify({ data: json })
                    })
                    .then(res => res.json())
                    .then(response => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 1800,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    })
                    .catch(err => {
                        Swal.fire('Error', 'Terjadi kesalahan saat menyimpan ke database.', 'error');
                    });
                }
            });
        };
        reader.readAsArrayBuffer(file);
        event.target.value = '';
    }
</script>
@endsection