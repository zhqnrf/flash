@extends('layouts.admin')
@section('title', 'Edit Master Evaluasi Materi')

@section('content')
<!-- Choices.js -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl md:text-3xl font-extrabold text-[#1a365d]">Edit Evaluasi Materi</h2>
        <p class="text-gray-500 text-sm mt-1">Perbarui data materi pelatihan FITC.</p>
    </div>
    <a href="{{ route('master.evaluasi-materi.index') }}" class="bg-white border border-gray-200 text-gray-700 font-bold py-2.5 px-6 rounded-xl shadow-sm">Kembali</a>
</div>

<div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 w-full">
    <form action="{{ route('master.evaluasi-materi.update', $evaluasiMateri->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid md:grid-cols-2 gap-6 mb-6">
            <!-- Pilih Pelatihan -->
            <div>
                <label class="block text-[#1a365d] text-sm font-bold mb-2">Nama Pelatihan</label>
                <select name="pelatihan_id" id="pelatihan_id" required>
                    <option value="">-- Pilih Pelatihan --</option>
                    @foreach($pelatihans as $pelatihan)
                        <option value="{{ $pelatihan->id }}" {{ $evaluasiMateri->pelatihan_id == $pelatihan->id ? 'selected' : '' }}>
                            {{ $pelatihan->nomor }} - {{ $pelatihan->nama_pelatihan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Nama Materi -->
            <div>
                <label class="block text-[#1a365d] text-sm font-bold mb-2">Nama Materi</label>
                <input type="text" name="nama_materi" value="{{ old('nama_materi', $evaluasiMateri->nama_materi) }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:border-[#1ba1e2] bg-gray-50 focus:bg-white" required>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6 mb-6">
            <!-- Unsur (Multi Select) -->
            <div>
                <label class="block text-[#1a365d] text-sm font-bold mb-2">Unsur (Bisa pilih lebih dari satu)</label>
                <select name="unsur[]" id="unsur" multiple required>
                    @php $selectedUnsurs = old('unsur', $evaluasiMateri->unsur ?? []); @endphp
                    @for($i=1; $i<=9; $i++)
                        <option value="U{{ $i }}" {{ in_array("U{$i}", $selectedUnsurs) ? 'selected' : '' }}>Unsur {{ $i }} (U{{ $i }})</option>
                    @endfor
                </select>
            </div>

            <!-- Tema -->
            <div>
                <label class="block text-[#1a365d] text-sm font-bold mb-2">Tema / Indikator</label>
                <select name="tema" id="tema" required>
                    <option value="">-- Pilih Tema --</option>
                    <option value="Kognitif" {{ $evaluasiMateri->tema == 'Kognitif' ? 'selected' : '' }}>Kognitif</option>
                    <option value="Psikomotorik" {{ $evaluasiMateri->tema == 'Psikomotorik' ? 'selected' : '' }}>Psikomotorik</option>
                    <option value="Afektif" {{ $evaluasiMateri->tema == 'Afektif' ? 'selected' : '' }}>Afektif</option>
                </select>
            </div>
        </div>

        <div class="grid md:grid-cols-3 gap-6 mb-6">
            <!-- Nilai Teori -->
            <div>
                <label class="block text-[#1a365d] text-sm font-bold mb-2">Nilai Teori</label>
                <input type="number" id="nilai_teori" name="nilai_teori" value="{{ old('nilai_teori', $evaluasiMateri->nilai_teori) }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white" required>
            </div>
            
            <!-- Nilai Praktik -->
            <div>
                <label class="block text-[#1a365d] text-sm font-bold mb-2">Nilai Praktik</label>
                <input type="number" id="nilai_praktik" name="nilai_praktik" value="{{ old('nilai_praktik', $evaluasiMateri->nilai_praktik) }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white" required>
            </div>

            <!-- JPL (Otomatis) -->
            <div>
                <label class="block text-gray-500 text-sm font-bold mb-2">JPL (Teori + Praktik)</label>
                <input type="number" id="jpl" value="{{ old('jpl', $evaluasiMateri->jpl) }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-100 font-bold text-gray-600" readonly>
                <p class="text-[10px] text-gray-400 mt-1">*Dihitung otomatis</p>
            </div>
        </div>

        <div class="grid md:grid-cols-3 gap-6 mb-8">
            <div>
                <label class="block text-[#1a365d] text-sm font-bold mb-2">Ambang Batas</label>
                <input type="number" step="0.01" name="ambang_batas" value="{{ old('ambang_batas', $evaluasiMateri->ambang_batas) }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white" required>
            </div>
            <div>
                <label class="block text-[#1a365d] text-sm font-bold mb-2">Rentang Nilai (Min)</label>
                <input type="number" name="rentang_nilai_min" value="{{ old('rentang_nilai_min', $evaluasiMateri->rentang_nilai_min) }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white" required>
            </div>
            <div>
                <label class="block text-[#1a365d] text-sm font-bold mb-2">Rentang Nilai (Max)</label>
                <input type="number" name="rentang_nilai_max" value="{{ old('rentang_nilai_max', $evaluasiMateri->rentang_nilai_max) }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white" required>
            </div>
        </div>

        <div class="border-t border-gray-100 pt-6">
            <button type="submit" class="bg-gradient-to-r from-[#1ba1e2] to-[#5bc0de] text-white font-bold py-4 px-12 rounded-xl shadow-lg">
                Perbarui Evaluasi Materi
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        new Choices('#pelatihan_id', { searchEnabled: true, placeholderValue: 'Cari Pelatihan...' });
        new Choices('#tema', { searchEnabled: false });
        new Choices('#unsur', { 
            removeItemButton: true,
            placeholderValue: 'Pilih unsur U1-U9',
            searchPlaceholderValue: 'Cari unsur...'
        });

        // Kalkulator JPL Otomatis di Form Edit
        const inputTeori = document.getElementById('nilai_teori');
        const inputPraktik = document.getElementById('nilai_praktik');
        const inputJpl = document.getElementById('jpl');

        function hitungJPL() {
            const teori = parseInt(inputTeori.value) || 0;
            const praktik = parseInt(inputPraktik.value) || 0;
            inputJpl.value = teori + praktik;
        }

        inputTeori.addEventListener('input', hitungJPL);
        inputPraktik.addEventListener('input', hitungJPL);
    });
</script>
@endsection