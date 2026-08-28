@extends('layouts.admin')
@section('title', 'Tambah Skill Materi')

@section('content')
<!-- Choices.js CSS & JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('error'))
<script>document.addEventListener('DOMContentLoaded', () => Swal.fire('Gagal', "{{ session('error') }}", 'error'));</script>
@endif

<div class="max-w-full bg-white p-8 md:p-10 rounded-2xl shadow-sm border border-gray-100">
    <div class="flex justify-between items-center mb-8 pb-4 border-b">
        <div>
            <h2 class="text-2xl font-extrabold text-[#1a365d]">Tambah Skill Materi</h2>
            <p class="text-gray-500 text-sm mt-1">Tentukan skill atau kompetensi yang dicakup oleh materi pelatihan.</p>
        </div>
        <a href="{{ route('master.skill-materi.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-5 py-2.5 rounded-xl font-bold text-sm">← Kembali</a>
    </div>

    <form action="{{ route('master.skill-materi.store') }}" method="POST" class="space-y-6">
        @csrf
        <div>
            <label class="block font-bold text-sm text-gray-700 mb-2">Pilih Materi Pelatihan <span class="text-red-500">*</span></label>
            <select name="evaluasi_materi_id" id="choices-materi" required class="w-full">
                <option value="">-- Pilih Materi Pelatihan --</option>
                @foreach($materis as $m)
                    <option value="{{ $m->id }}">
                        {{ $m->nama_materi }} (Rentang Nilai: {{ $m->rentang_nilai_min }} - {{ $m->rentang_nilai_max }})
                    </option>
                @endforeach
            </select>
            <p class="text-xs text-gray-400 mt-1.5 font-medium">* Rentang nilai min & max akan otomatis mengikuti master materi yang dipilih.</p>
        </div>

        <div>
            <label class="block font-bold text-sm text-gray-700 mb-2">Nama Skill / Kompetensi <span class="text-red-500">*</span></label>
            <input type="text" name="nama_skill" required class="w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl font-medium text-sm focus:outline-none focus:border-[#1ba1e2]" placeholder="Contoh: Ketepatan Analisis Kasus">
        </div>

        <div class="flex justify-end gap-3 pt-6 border-t">
            <a href="{{ route('master.skill-materi.index') }}" class="bg-gray-100 px-6 py-3 rounded-xl font-bold text-sm">Batal</a>
            <button type="submit" class="bg-[#1a365d] hover:bg-[#1ba1e2] text-white px-8 py-3 rounded-xl font-bold text-sm shadow-lg">Simpan Skill</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const element = document.getElementById('choices-materi');
        if(element) {
            new Choices(element, {
                removeItemButton: true,
                placeholder: true,
                placeholderValue: 'Cari dan pilih materi...',
                itemSelectText: 'Tekan untuk pilih',
            });
        }
    });
</script>
@endsection