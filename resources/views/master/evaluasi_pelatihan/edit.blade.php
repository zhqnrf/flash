@extends('layouts.admin')
@section('title', 'Edit Evaluasi Pelatihan')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

<div class="mb-6 flex justify-between items-center">
    <h2 class="text-2xl md:text-3xl font-extrabold text-[#1a365d]">Edit Evaluasi Pelatihan</h2>
    <a href="{{ route('master.evaluasi-pelatihan.index') }}" class="bg-white border border-gray-200 text-gray-700 font-bold py-2.5 px-6 rounded-xl shadow-sm">Kembali</a>
</div>

<div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 md:p-10 w-full">
    <form action="{{ route('master.evaluasi-pelatihan.update', $evaluasiPelatihan->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="grid md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-[#1a365d] text-sm font-bold mb-2">Nama / ID Pelatihan</label>
             <select name="pelatihan_id" id="pelatihan_id">
    <option value="">-- Semua Pelatihan (Berlaku untuk Semua) --</option>
    @foreach($pelatihans as $p)
        <option value="{{ $p->id }}" {{ isset($evaluasiPelatihan) && $evaluasiPelatihan->pelatihan_id == $p->id ? 'selected' : '' }}>
            {{ $p->nomor }} - {{ $p->nama_pelatihan }}
        </option>
    @endforeach
</select>
            </div>
            <div>
                <label class="block text-[#1a365d] text-sm font-bold mb-2">Jenis Evaluasi</label>
                <input type="text" name="jenis_evaluasi" value="{{ old('jenis_evaluasi', $evaluasiPelatihan->jenis_evaluasi) }}" class="w-full px-5 py-3.5 rounded-xl border border-gray-200 focus:outline-none focus:border-[#1ba1e2] bg-gray-50 focus:bg-white" required>
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-[#1a365d] text-sm font-bold mb-2">Nama Evaluasi</label>
            <input type="text" name="nama_evaluasi" value="{{ old('nama_evaluasi', $evaluasiPelatihan->nama_evaluasi) }}" class="w-full px-5 py-3.5 rounded-xl border border-gray-200 focus:outline-none focus:border-[#1ba1e2] bg-gray-50 focus:bg-white" required>
        </div>

        <div class="grid md:grid-cols-2 gap-6 mb-8">
            <div>
                <label class="block text-[#1a365d] text-sm font-bold mb-2">Rentang Nilai (Dari / Min)</label>
                <input type="number" name="rentang_nilai_min" value="{{ old('rentang_nilai_min', $evaluasiPelatihan->rentang_nilai_min) }}" class="w-full px-5 py-3.5 rounded-xl border border-gray-200 focus:outline-none focus:border-[#1ba1e2] bg-gray-50 focus:bg-white" required>
            </div>
            <div>
                <label class="block text-[#1a365d] text-sm font-bold mb-2">Rentang Nilai (Sampai / Max)</label>
                <input type="number" name="rentang_nilai_max" value="{{ old('rentang_nilai_max', $evaluasiPelatihan->rentang_nilai_max) }}" class="w-full px-5 py-3.5 rounded-xl border border-gray-200 focus:outline-none focus:border-[#1ba1e2] bg-gray-50 focus:bg-white" required>
            </div>
        </div>

        <div class="border-t border-gray-100 pt-6">
            <button type="submit" class="bg-gradient-to-r from-[#1ba1e2] to-[#5bc0de] text-white font-bold py-4 px-12 rounded-xl shadow-lg">Perbarui Data</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        new Choices('#pelatihan_id', { searchEnabled: true, placeholderValue: 'Cari Pelatihan...' });
    });
</script>
@endsection