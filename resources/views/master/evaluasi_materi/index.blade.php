@extends('layouts.admin')
@section('title', 'Master Evaluasi Materi')

@section('content')
<!-- CDN SheetJS (xlsx) untuk Excel Handler -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<!-- Header Halaman -->
<div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-end gap-4">
    <div>
        <h2 class="text-2xl md:text-3xl font-extrabold text-[#1a365d]">Master Evaluasi Materi</h2>
        <p class="text-gray-500 text-sm mt-1">Kelola data materi pelatihan, unsur U1-U9, JPL, ambang batas nilai, dan sinkronisasi Excel.</p>
    </div>
    
    <!-- Tombol Aksi Excel & Tambah -->
    <div class="flex flex-wrap gap-2 items-center">
        <!-- Download Template Excel -->
        <button onclick="downloadTemplateMateri()" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-3 rounded-xl font-bold transition-all shadow-md text-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            Template Excel
        </button>

        <!-- Import Excel -->
        <label class="bg-purple-600 hover:bg-purple-500 text-white px-4 py-3 rounded-xl font-bold transition-all shadow-md text-sm cursor-pointer flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
            Import Excel
            <input type="file" id="import_file" accept=".xlsx, .xls, .csv" class="hidden" onchange="handleImportMateri(event)">
        </label>

        <!-- Export Excel (Backend Full Data) -->
        <button onclick="exportExcelMateri()" class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-3 rounded-xl font-bold transition-all shadow-md text-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Export Excel
        </button>

        <!-- Tambah Data -->
        <a href="{{ route('master.evaluasi-materi.create') }}" class="bg-[#1a365d] hover:bg-[#1ba1e2] text-white px-5 py-3 rounded-xl font-bold transition-all shadow-lg text-sm whitespace-nowrap">
            + Tambah Evaluasi Materi
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

<!-- Baris Pencarian (Search) -->
<div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6">
    <form action="{{ route('master.evaluasi-materi.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center">
        <div class="relative flex-1 w-full">
            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}" class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#1ba1e2] text-sm font-medium" placeholder="Cari nama materi atau pelatihan...">
        </div>
        <div class="w-full md:w-auto flex gap-3">
            <button type="submit" class="bg-[#1ba1e2] hover:bg-blue-500 text-white px-6 py-3 rounded-xl font-bold shadow-md">Cari</button>
            @if(request('search'))
            <a href="{{ route('master.evaluasi-materi.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-500 px-4 py-3 rounded-xl font-bold flex items-center">Reset</a>
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
                    <th class="p-5 font-extrabold">Nama Pelatihan</th>
                    <th class="p-5 font-extrabold">Nama Materi</th>
                    <th class="p-5 font-extrabold">Unsur</th>
                    <th class="p-5 font-extrabold text-center">Tema</th>
                    <th class="p-5 font-extrabold text-center">Teori / Praktik / JPL</th>
                    <th class="p-5 font-extrabold text-center">Ambang Batas</th>
                    <th class="p-5 font-extrabold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                @forelse($materis as $index => $item)
                <tr class="hover:bg-blue-50/50 border-b border-gray-50 transition-colors">
                    <td class="p-5 text-center font-semibold text-gray-400">
                        {{ $loop->iteration }}
                    </td>
                    <td class="p-5 font-bold text-[#1a365d]">{{ $item->pelatihan->nama_pelatihan ?? '-' }}</td>
                    <td class="p-5 font-medium text-gray-800">{{ $item->nama_materi }}</td>
                    <td class="p-5">
                        <div class="flex flex-wrap gap-1">
                            @if(is_array($item->unsur))
                                @foreach($item->unsur as $u)
                                    <span class="bg-cyan-50 text-cyan-700 text-xs font-bold px-2 py-0.5 rounded border border-cyan-100">{{ $u }}</span>
                                @endforeach
                            @else
                                <span class="text-xs text-gray-400">-</span>
                            @endif
                        </div>
                    </td>
                    <td class="p-5 text-center">
                        <span class="bg-purple-50 text-purple-600 px-3 py-1 rounded-full text-xs font-bold">{{ $item->tema }}</span>
                    </td>
                    <td class="p-5 text-center font-semibold text-gray-600">
                        {{ $item->nilai_teori }} / {{ $item->nilai_praktik }} / <span class="text-blue-600 font-bold">{{ $item->jpl }}</span>
                    </td>
                    <td class="p-5 text-center font-bold text-gray-600">{{ $item->ambang_batas }}</td>
                    <td class="p-5 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <!-- Tombol Edit -->
                            <a href="{{ route('master.evaluasi-materi.edit', $item->id) }}" class="bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white p-2 rounded-lg transition-colors border border-amber-200" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </a>
                            
                            <!-- Tombol Hapus (SweetAlert2) -->
                            <form id="delete-form-{{ $item->id }}" action="{{ route('master.evaluasi-materi.destroy', $item->id) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmDeleteMateri('{{ $item->id }}', '{{ addslashes($item->nama_materi) }}')" class="bg-red-50 text-red-500 hover:bg-red-500 hover:text-white p-2 rounded-lg transition-colors border border-red-200" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="p-10 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <p class="text-gray-500 font-bold text-lg">Data Tidak Ditemukan</p>
                            <p class="text-gray-400 text-sm mt-1">Belum ada data evaluasi materi yang tersedia.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Script Excel & SweetAlert Handler -->
<script>
 function downloadTemplateMateri() {
        const templateData = [{
            "Nama Pelatihan": "Contoh Nama Pelatihan yang Sudah Ada", // Sesuaikan dengan nama pelatihan yang ada di DB
            "Nama Materi": "Contoh Nama Materi Pelatihan",
            "Unsur (Pisahkan koma misal: U1,U2)": "U1,U2",
            "Ambang Batas": 70,
            "Tema": "Kognitif",
            "Nilai Teori": 50,
            "Nilai Praktik": 50,
            "Rentang Min": 0,
            "Rentang Max": 100
        }];
        const ws = XLSX.utils.json_to_sheet(templateData);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Template Evaluasi Materi");
        XLSX.writeFile(wb, "Template_Master_Evaluasi_Materi.xlsx");
    }

    // 2. Export Seluruh Data dari Backend via SheetJS
    function exportExcelMateri() {
        Swal.fire({ title: 'Menyiapkan seluruh data...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
        
        fetch("{{ route('master.evaluasi-materi.export') }}")
            .then(res => res.json())
            .then(data => {
                Swal.close();
                if (data.length === 0) {
                    Swal.fire('Info', 'Tidak ada data untuk diexport.', 'info');
                    return;
                }
                const ws = XLSX.utils.json_to_sheet(data);
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, "Data Evaluasi Materi");
                XLSX.writeFile(wb, "Semua_Data_Evaluasi_Materi.xlsx");
            })
            .catch(err => {
                Swal.fire('Error', 'Gagal mengambil data dari server.', 'error');
            });
    }

    // 3. Import Excel ke Backend dengan Anti-duplikasi
    function handleImportMateri(event) {
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
                text: `Akan memproses ${jsonkr.length} baris data ke server (data materi yang sama persis otomatis diperbarui). Lanjutkan?`,
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
                    Swal.fire({ title: 'Mengimpor ke database...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

                    fetch("{{ route('master.evaluasi-materi.import') }}", {
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
        event.target.value = '';
    }

    // 4. Konfirmasi Hapus Data via SweetAlert2
    function confirmDeleteMateri(id, name) {
        Swal.fire({
            title: 'Hapus Evaluasi Materi?',
            html: "Yakin ingin menghapus materi <b>" + name + "</b>?",
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