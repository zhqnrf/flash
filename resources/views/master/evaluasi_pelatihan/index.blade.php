@extends('layouts.admin')
@section('title', 'Master Evaluasi Pelatihan')

@section('content')
<!-- CDN SheetJS -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<!-- Header Halaman -->
<div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-end gap-4">
    <div>
        <h2 class="text-2xl md:text-3xl font-extrabold text-[#1a365d]">Master Evaluasi Pelatihan</h2>
        <p class="text-gray-500 text-sm mt-1">Kelola jenis dan nama evaluasi pelatihan beserta rentang nilainya.</p>
    </div>
    
    <!-- Tombol Aksi Excel & Tambah -->
    <div class="flex flex-wrap gap-2 items-center">
        <!-- Template Excel -->
        <button onclick="downloadTemplatePelatihan()" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-3 rounded-xl font-bold transition-all shadow-md text-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            Template Excel
        </button>

        <!-- Import Excel -->
        <label class="bg-purple-600 hover:bg-purple-500 text-white px-4 py-3 rounded-xl font-bold transition-all shadow-md text-sm cursor-pointer flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
            Import Excel
            <input type="file" id="import_file" accept=".xlsx, .xls, .csv" class="hidden" onchange="handleImportPelatihan(event)">
        </label>

        <!-- Export Excel -->
        <button onclick="exportExcelPelatihan()" class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-3 rounded-xl font-bold transition-all shadow-md text-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Export Excel
        </button>

        <a href="{{ route('master.evaluasi-pelatihan.create') }}" class="bg-[#1a365d] hover:bg-[#1ba1e2] text-white px-5 py-3 rounded-xl font-bold transition-all shadow-lg text-sm whitespace-nowrap">
            + Tambah Evaluasi
        </a>
    </div>
</div>

<!-- Notifikasi -->
@if(session('success'))
<div x-data="{ show: true }" x-show="show" class="bg-green-50 border border-green-200 text-green-700 p-4 mb-6 rounded-xl shadow-sm flex justify-between items-center">
    <span class="font-bold">{{ session('success') }}</span>
    <button @click="show = false" class="text-green-500 hover:text-green-700">✕</button>
</div>
@endif

<!-- Search Bar -->
<div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6">
    <form action="{{ route('master.evaluasi-pelatihan.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center">
        <div class="relative flex-1 w-full">
            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}" class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#1ba1e2] text-sm font-medium" placeholder="Cari jenis, nama evaluasi, atau pelatihan...">
        </div>
        <div class="w-full md:w-auto flex gap-3">
            <button type="submit" class="bg-[#1ba1e2] hover:bg-blue-500 text-white px-6 py-3 rounded-xl font-bold shadow-md">Cari</button>
            @if(request('search'))
            <a href="{{ route('master.evaluasi-pelatihan.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-500 px-4 py-3 rounded-xl font-bold flex items-center">Reset</a>
            @endif
        </div>
    </form>
</div>

<!-- Tabel Data -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table id="evaluasi-pelatihan-table" class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-slate-50 text-[#1a365d] text-sm uppercase tracking-wider border-b border-gray-100">
                    <th class="p-5 font-extrabold w-16 text-center">No</th>
                    <th class="p-5 font-extrabold">Nama Pelatihan</th>
                    <th class="p-5 font-extrabold">Jenis Evaluasi</th>
                    <th class="p-5 font-extrabold">Nama Evaluasi</th>
                    <th class="p-5 font-extrabold text-center">Rentang Nilai</th>
                    <th class="p-5 font-extrabold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                @forelse($evaluasis as $index => $item)
                <tr class="hover:bg-blue-50/50 border-b border-gray-50 transition-colors">
                    <td class="p-5 text-center font-semibold text-gray-400">
                        {{ $evaluasis->firstItem() + $index }}
                    </td>
                    <td class="p-5 font-bold text-[#1a365d]">{{ $item->pelatihan->nama_pelatihan ?? 'Semua Pelatihan (Global)' }}</td>
                    <td class="p-5"><span class="bg-blue-50 text-blue-600 px-3 py-1 rounded-md text-xs font-bold">{{ $item->jenis_evaluasi }}</span></td>
                    <td class="p-5 font-medium">{{ $item->nama_evaluasi }}</td>
                    <td class="p-5 text-center font-bold text-gray-600">{{ $item->rentang_nilai_min }} s/d {{ $item->rentang_nilai_max }}</td>
                    <td class="p-5 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('master.evaluasi-pelatihan.edit', $item->id) }}" class="bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white p-2 rounded-lg transition-colors border border-amber-200" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </a>
                            
                            <form id="delete-form-{{ $item->id }}" action="{{ route('master.evaluasi-pelatihan.destroy', $item->id) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmDeleteEvaluasiPelatihan('{{ $item->id }}', '{{ addslashes($item->nama_evaluasi) }}')" class="bg-red-50 text-red-500 hover:bg-red-500 hover:text-white p-2 rounded-lg transition-colors border border-red-200" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-10 text-center text-gray-400 font-medium">Belum ada data evaluasi pelatihan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($evaluasis->hasPages())
    <div class="p-5 border-t border-gray-100 bg-gray-50">
        {{ $evaluasis->links() }}
    </div>
    @endif
</div>

<!-- Script SheetJS & SweetAlert untuk Evaluasi Pelatihan -->
<script>
    // 1. Download Template Excel Khusus Evaluasi Pelatihan
    function downloadTemplatePelatihan() {
        const templateData = [
            { 
                pelatihan_id: "", 
                jenis_evaluasi: "Kinerja", 
                nama_evaluasi: "Evaluasi Kinerja Peserta Pelatihan", 
                rentang_nilai_min: 0, 
                rentang_nilai_max: 100 
            }
        ];
        const ws = XLSX.utils.json_to_sheet(templateData);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Template Evaluasi Pelatihan");
        XLSX.writeFile(wb, "Template_Master_Evaluasi_Pelatihan.xlsx");
    }

    // 2. Export Seluruh Data dari Backend (Mengambil semua data dari database tanpa terhalang pagination)
    function exportExcelPelatihan() {
        Swal.fire({ 
            title: 'Menyiapkan seluruh data...', 
            allowOutsideClick: false, 
            didOpen: () => { Swal.showLoading(); } 
        });

        fetch("{{ route('master.evaluasi-pelatihan.export') }}")
            .then(res => res.json())
            .then(data => {
                Swal.close();
                if (data.length === 0) {
                    Swal.fire('Info', 'Tidak ada data untuk diexport.', 'info');
                    return;
                }
                const ws = XLSX.utils.json_to_sheet(data);
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, "Data Evaluasi Pelatihan");
                XLSX.writeFile(wb, "Semua_Data_Evaluasi_Pelatihan.xlsx");
            })
            .catch(err => {
                Swal.fire('Error', 'Gagal mengambil data dari server.', 'error');
            });
    }

    // 3. Import Data ke Backend (Membaca file excel di browser, lalu dikirim ke Controller untuk diproses anti-duplikasinya)
    function handleImportPelatihan(event) {
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
                Swal.fire('Gagal', 'File Excel kosong atau format tidak sesuai!', 'error');
                return;
            }

            Swal.fire({
                title: 'Konfirmasi Import Backend',
                text: `Akan memproses ${jsonkr.length} baris data ke server (data yang sama persis otomatis diperbarui). Lanjutkan?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Proses Import!',
                cancelButtonText: 'Batal',
                customClass: { 
                    popup: 'rounded-2xl', 
                    confirmButton: 'rounded-xl px-6 py-2.5 font-bold', 
                    cancelButton: 'rounded-xl px-6 py-2.5 font-bold' 
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ 
                        title: 'Mengimpor ke database...', 
                        allowOutsideClick: false, 
                        didOpen: () => { Swal.showLoading(); } 
                    });

                    fetch("{{ route('master.evaluasi-pelatihan.import') }}", {
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
                        Swal.fire('Error', 'Terjadi kesalahan saat menyimpan ke database.', 'error');
                    });
                }
            });
        };
        reader.readAsArrayBuffer(file);
        
        // Reset nilai input file agar bisa memilih file yang sama berulang kali jika perlu
        event.target.value = '';
    }

    // 4. Konfirmasi Hapus Data via SweetAlert2
    function confirmDeleteEvaluasiPelatihan(id, name) {
        Swal.fire({
            title: 'Hapus Evaluasi?',
            html: "Yakin ingin menghapus <b>" + name + "</b>?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: { 
                popup: 'rounded-2xl', 
                confirmButton: 'rounded-xl px-6 py-2.5 font-bold', 
                cancelButton: 'rounded-xl px-6 py-2.5 font-bold' 
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>
@endsection