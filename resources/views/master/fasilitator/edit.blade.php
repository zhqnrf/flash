@extends('layouts.admin')
@section('title', 'Edit Fasilitator')

@section('content')
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'error',
            title: 'Gagal Memperbarui!',
            text: "{{ session('error') }}",
            confirmButtonColor: '#1a365d',
            customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl px-6 py-2.5 font-bold' }
        });
    });
</script>
@endif

<div class="max-w-full bg-white p-8 md:p-10 rounded-2xl shadow-sm border border-gray-100">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8 pb-4 border-b border-gray-100">
        <div>
            <h2 class="text-2xl font-extrabold text-[#1a365d]">Edit Master Fasilitator</h2>
            <p class="text-gray-500 text-sm mt-1">Perbarui informasi data pengajar / fasilitator.</p>
        </div>
        <a href="{{ route('master.fasilitator.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-5 py-2.5 rounded-xl font-bold text-sm transition-all">← Kembali</a>
    </div>

    <form action="{{ route('master.fasilitator.update', $fasilitator->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nama Fasilitator -->
            <div class="md:col-span-2">
                <label class="block font-bold text-sm text-gray-700 mb-2">Nama Lengkap & Gelar <span class="text-red-500">*</span></label>
                <input type="text" name="nama_fasilitator" value="{{ old('nama_fasilitator', $fasilitator->nama_fasilitator) }}" required class="w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#1ba1e2] font-medium text-sm">
            </div>

            <!-- Profesi -->
            <div>
                <label class="block font-bold text-sm text-gray-700 mb-2">Profesi</label>
                <input type="text" name="profesi_fasilitator" value="{{ old('profesi_fasilitator', $fasilitator->profesi_fasilitator) }}" class="w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#1ba1e2] font-medium text-sm">
            </div>

            <!-- Jabatan -->
            <div>
                <label class="block font-bold text-sm text-gray-700 mb-2">Jabatan</label>
                <input type="text" name="jabatan_fasilitator" value="{{ old('jabatan_fasilitator', $fasilitator->jabatan_fasilitator) }}" class="w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#1ba1e2] font-medium text-sm">
            </div>

            <!-- Tempat Kerja -->
            <div>
                <label class="block font-bold text-sm text-gray-700 mb-2">Tempat Kerja</label>
                <input type="text" name="tempat_kerja_fasilitator" value="{{ old('tempat_kerja_fasilitator', $fasilitator->tempat_kerja_fasilitator) }}" class="w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#1ba1e2] font-medium text-sm">
            </div>

            <!-- Pendidikan Terakhir -->
            <div>
                <label class="block font-bold text-sm text-gray-700 mb-2">Pendidikan Terakhir</label>
                <input type="text" name="pendidikan_terakhir_fasilitator" value="{{ old('pendidikan_terakhir_fasilitator', $fasilitator->pendidikan_terakhir_fasilitator) }}" class="w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#1ba1e2] font-medium text-sm">
            </div>

            <!-- Upload Dokumen -->
            <div>
                <label class="block font-bold text-sm text-gray-700 mb-2">Dokumen Pendukung Baru (Opsional)</label>
                <div class="relative border-2 border-dashed border-gray-200 rounded-2xl p-4 bg-gray-50 hover:bg-gray-100/50 transition-all text-center cursor-pointer">
                    <input type="file" name="dokumen_fasilitator" accept=".pdf,.doc,.docx" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full" onchange="validateAndPreviewDoc(this, 'doc-label')">
                    <div class="flex flex-col items-center justify-center space-y-1">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        <p id="doc-label" class="text-sm font-bold text-gray-600">
                            @if($fasilitator->dokumen_fasilitator) Ganti dokumen (File tersimpan) @else Klik untuk unggah dokumen @endif
                        </p>
                        <p class="text-xs text-gray-400">PDF, DOC, DOCX (Maks. 1MB)</p>
                    </div>
                </div>
            </div>

            <!-- Upload Foto 3x4 -->
            <div>
                <label class="block font-bold text-sm text-gray-700 mb-2">Foto 3x4 Baru (Opsional)</label>
                <div class="flex items-center gap-4">
                    <div class="w-16 h-20 bg-gray-100 border rounded-xl overflow-hidden flex items-center justify-center shrink-0">
                        @if($fasilitator->foto_fasilitator)
                            <img id="foto-preview" src="{{ asset('storage/' . $fasilitator->foto_fasilitator) }}" class="w-full h-full object-cover">
                            <svg id="foto-placeholder" class="w-6 h-6 text-gray-400 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        @else
                            <img id="foto-preview" class="w-full h-full object-cover hidden">
                            <svg id="foto-placeholder" class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        @endif
                    </div>
                    <div class="relative flex-1 border-2 border-dashed border-gray-200 rounded-2xl p-3 bg-gray-50 hover:bg-gray-100/50 transition-all text-center cursor-pointer">
                        <input type="file" name="foto_fasilitator" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full" onchange="validateAndPreviewFoto(this)">
                        <p id="foto-label" class="text-xs font-bold text-gray-600">Ganti Foto 3x4</p>
                        <p class="text-[10px] text-gray-400">JPG, PNG (Maks. 1MB)</p>
                    </div>
                </div>
            </div>

            <!-- Materi Yang Diampu (Choices.js) -->
            <div class="md:col-span-2">
                <label class="block font-bold text-sm text-gray-700 mb-2">Materi Yang Diampu</label>
                <select name="materi_ids[]" id="choices-materi" multiple class="w-full">
                    @foreach($materis as $m)
                        <option value="{{ $m->id }}" {{ $fasilitator->materis->contains($m->id) ? 'selected' : '' }}>
                            {{ $m->nama_materi }} (Ambang Batas: {{ $m->ambang_batas }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
            <a href="{{ route('master.fasilitator.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-6 py-3 rounded-xl font-bold text-sm transition-all">Batal</a>
            <button type="submit" class="bg-[#1a365d] hover:bg-[#1ba1e2] text-white px-8 py-3 rounded-xl font-bold text-sm transition-all shadow-lg">Simpan Perubahan</button>
        </div>
    </form>
</div>

<!-- Script Choices.js & Preview Validasi Max 1MB -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const element = document.getElementById('choices-materi');
        if(element) {
            new Choices(element, {
                removeItemButton: true,
                placeholder: true,
                itemSelectText: 'Tekan untuk pilih',
            });
        }
    });

    function validateAndPreviewDoc(input, labelId) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const maxSize = 1 * 1024 * 1024; // 1 MB

            if (file.size > maxSize) {
                Swal.fire({
                    icon: 'warning',
                    title: 'File Terlalu Besar!',
                    text: `Ukuran file "${file.name}" adalah ${(file.size / (1024*1024)).toFixed(2)} MB. Maksimal ukuran yang diizinkan adalah 1 MB!`,
                    confirmButtonColor: '#1a365d',
                    customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl px-6 py-2.5 font-bold' }
                });
                input.value = '';
                document.getElementById(labelId).innerText = 'Ganti dokumen (File tersimpan)';
                return;
            }
            document.getElementById(labelId).innerText = file.name;
        }
    }

    function validateAndPreviewFoto(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const maxSize = 1 * 1024 * 1024; // 1 MB

            if (file.size > maxSize) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Foto Terlalu Besar!',
                    text: `Ukuran foto "${file.name}" adalah ${(file.size / (1024*1024)).toFixed(2)} MB. Maksimal ukuran foto adalah 1 MB!`,
                    confirmButtonColor: '#1a365d',
                    customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl px-6 py-2.5 font-bold' }
                });
                input.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('foto-preview');
                const placeholder = document.getElementById('foto-placeholder');
                if(preview) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                }
                if(placeholder) {
                    placeholder.classList.add('hidden');
                }
                const label = document.getElementById('foto-label');
                if(label) {
                    label.innerText = file.name;
                }
            }
            reader.readAsDataURL(file);
        }
    }
</script>
@endsection