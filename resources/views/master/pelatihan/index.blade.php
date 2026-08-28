@extends('layouts.admin')
@section('title', 'Master Pelatihan')

@section('content')
<!-- CDN SheetJS -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<!-- Header Halaman -->
<div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-end gap-4">
    <div>
        <h2 class="text-2xl md:text-3xl font-extrabold text-[#1a365d]">Master Pelatihan</h2>
        <p class="text-gray-500 text-sm mt-1">Kelola data nomor dan nama program pelatihan FITC, cari, urutkan, atau kelola via Excel.</p>
    </div>
    
    <!-- Tombol Aksi: Template, Import, Export, Tambah -->
    <div class="flex flex-wrap gap-2 items-center">
        <!-- Tombol Download Template -->
        <button onclick="downloadTemplate()" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-3 rounded-xl font-bold transition-all shadow-md text-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            Template Excel
        </button>

        <!-- Tombol Import Excel -->
        <label class="bg-purple-600 hover:bg-purple-500 text-white px-4 py-3 rounded-xl font-bold transition-all shadow-md text-sm cursor-pointer flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
            Import Excel
            <input type="file" id="import_file" accept=".xlsx, .xls, .csv" class="hidden" onchange="handleImport(event)">
        </label>

        <!-- Tombol Export Excel -->
        <button onclick="exportExcel()" class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-3 rounded-xl font-bold transition-all shadow-md text-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Export Excel
        </button>

        <a href="{{ route('master.pelatihan.create') }}" class="bg-[#1a365d] hover:bg-[#1ba1e2] text-white px-5 py-3 rounded-xl font-bold transition-all shadow-lg text-sm">
            + Tambah
        </a>
    </div>
</div>

<!-- Notifikasi Sukses -->
@if(session('success'))
<div x-data="{ show: true }" x-show="show" class="bg-green-50 border border-green-200 text-green-700 p-4 mb-6 rounded-xl shadow-sm flex justify-between items-center">
    <span class="font-bold">{{ session('success') }}</span>
    <button @click="show = false" class="text-green-500 hover:text-green-700">✕</button>
</div>
@endif

<!-- Baris Pencarian & Filter (SEARCH & SORT DIKEMBALIKAN DI SINI) -->
<div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6">
    <form action="{{ route('master.pelatihan.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center">
        
        <!-- Search Input -->
        <div class="relative flex-1 w-full">
            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}" class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#1ba1e2]/20 focus:border-[#1ba1e2] transition-colors text-sm font-medium" placeholder="Cari nomor atau nama pelatihan...">
        </div>

        <!-- Filter / Sort Dropdown -->
        <div class="w-full md:w-auto flex gap-3">
            <select name="sort" class="w-full md:w-48 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#1ba1e2]/20 focus:border-[#1ba1e2] transition-colors text-sm font-semibold text-gray-700 cursor-pointer">
                <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>Terbaru (Default)</option>
                <option value="terlama" {{ request('sort') == 'terlama' ? 'selected' : '' }}>Terlama</option>
                <option value="az" {{ request('sort') == 'az' ? 'selected' : '' }}>Nama (A - Z)</option>
                <option value="za" {{ request('sort') == 'za' ? 'selected' : '' }}>Nama (Z - A)</option>
            </select>
            
            <button type="submit" class="bg-[#1ba1e2] hover:bg-blue-500 text-white px-6 py-3 rounded-xl font-bold transition-all shadow-md">
                Terapkan
            </button>
            
            @if(request('search') || request('sort'))
            <a href="{{ route('master.pelatihan.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-500 px-4 py-3 rounded-xl font-bold transition-all flex items-center" title="Reset Filter">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            </a>
            @endif
        </div>
    </form>
</div>

<!-- Tabel Data -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table id="pelatihan-table" class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-slate-50 text-[#1a365d] text-sm uppercase tracking-wider border-b border-gray-100">
                    <th class="p-5 font-extrabold w-16 text-center">No</th>
                    <th class="p-5 font-extrabold">Nomor Pelatihan</th>
                    <th class="p-5 font-extrabold">Nama Pelatihan</th>
                    <th class="p-5 font-extrabold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                @forelse($pelatihans as $index => $item)
                <tr class="hover:bg-blue-50/50 border-b border-gray-50 transition-colors">
                    <td class="p-5 text-center font-semibold text-gray-400">
                        {{ $pelatihans->firstItem() + $index }}
                    </td>
                    <td class="p-5 font-bold text-[#1ba1e2]">{{ $item->nomor }}</td>
                    <td class="p-5 font-bold text-[#1a365d]">{{ $item->nama_pelatihan }}</td>
                    <td class="p-5 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('master.pelatihan.edit', $item->id) }}" class="bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white p-2 rounded-lg transition-colors border border-amber-200" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </a>
                            
                            <form id="delete-form-{{ $item->id }}" action="{{ route('master.pelatihan.destroy', $item->id) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmDeletePelatihan('{{ $item->id }}', '{{ addslashes($item->nama_pelatihan) }}')" class="bg-red-50 text-red-500 hover:bg-red-500 hover:text-white p-2 rounded-lg transition-colors border border-red-200" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-10 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <p class="text-gray-500 font-bold text-lg">Data Tidak Ditemukan</p>
                            <p class="text-gray-400 text-sm mt-1">Coba gunakan kata kunci pencarian yang lain.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginasi -->
    @if($pelatihans->hasPages())
    <div class="p-5 border-t border-gray-100 bg-gray-50">
        {{ $pelatihans->links() }}
    </div>
    @endif
</div>

<!-- Script SheetJS & SweetAlert -->
<script>
    function downloadTemplate() {
        const templateData = [
            { nomor: "SK/001/2026", nama_pelatihan: "Contoh Nama Pelatihan BTCLS" }
        ];
        const ws = XLSX.utils.json_to_sheet(templateData);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Template Pelatihan");
        XLSX.writeFile(wb, "Template_Master_Pelatihan.xlsx");
    }

   // Export Backend (Mengambil SELURUH data dari database via Controller)
    function exportExcel() {
        Swal.fire({ title: 'Menyiapkan data...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

        fetch("{{ route('master.pelatihan.export') }}")
            .then(res => res.json())
            .then(data => {
                Swal.close();
                if (data.length === 0) {
                    Swal.fire('Info', 'Tidak ada data untuk diexport.', 'info');
                    return;
                }
                const ws = XLSX.utils.json_to_sheet(data);
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, "Data Pelatihan");
                XLSX.writeFile(wb, "Semua_Data_Pelatihan.xlsx");
            })
            .catch(err => {
                Swal.fire('Error', 'Gagal mengambil data dari server.', 'error');
            });
    }

    // Import Backend (Mengirim data excel ke Controller untuk disimpan & dicek duplikasinya)
    function handleImport(event) {
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            const data = new Uint8Array(e.target.result);
            const workbook = XLSX.read(data, { type: 'array' });
            const firstSheetName = workbook.SheetNames[0];
            const worksheet = workbook.Sheets[firstSheetName];
            const jsonkr = XLSX.utils.sheet_to_json(worksheet);

            if (jsonkr.length === 0) {
                Swal.fire('Gagal', 'File Excel kosong!', 'error');
                return;
            }

            Swal.fire({
                title: 'Konfirmasi Import Backend',
                text: `Akan memproses ${jsonkr.length} baris data ke server (data kembar akan otomatis diperbarui). Lanjutkan?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Proses!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Mengimpor ke database...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

                    fetch("{{ route('master.pelatihan.import') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({ data: jsonkr })
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
                        Swal.fire('Error', 'Terjadi kesalahan pada server.', 'error');
                    });
                }
            });
        };
        reader.readAsArrayBuffer(file);
        event.target.value = '';
    }

    function confirmDeletePelatihan(id, name) {
        Swal.fire({
            title: 'Hapus Pelatihan?',
            html: "Yakin ingin menghapus <b>" + name + "</b>?",
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
        })
    }
</script>
@endsection