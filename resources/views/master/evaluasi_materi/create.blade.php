@extends('layouts.admin')
@section('title', 'Tambah Master Evaluasi Materi')

@section('content')
<!-- Memanggil Library Choices.js -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

<div class="mb-6">
    <h2 class="text-2xl font-extrabold text-[#1a365d]">Tambah Evaluasi Materi</h2>
    <p class="text-gray-500 text-sm">Master form untuk materi pelatihan FITC.</p>
</div>

<div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8">
    <form action="{{ route('master.evaluasi-materi.store') }}" method="POST">
        @csrf
        
        <div class="grid md:grid-cols-2 gap-6 mb-6">
            <!-- Pilih Pelatihan -->
            <div>
                <label class="block text-[#1a365d] text-sm font-bold mb-2">Nama Pelatihan</label>
                <select name="pelatihan_id" id="pelatihan_id" class="w-full" required>
                    <option value="">-- Pilih Pelatihan --</option>
                    @foreach($pelatihans as $pelatihan)
                        <option value="{{ $pelatihan->id }}">{{ $pelatihan->nomor }} - {{ $pelatihan->nama_pelatihan }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Nama Materi -->
            <div>
                <label class="block text-[#1a365d] text-sm font-bold mb-2">Nama Materi</label>
                <input type="text" name="nama_materi" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#1ba1e2]/20" required>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6 mb-6">
            <!-- Unsur (Multi Select) -->
            <div>
                <label class="block text-[#1a365d] text-sm font-bold mb-2">Unsur (Bisa pilih lebih dari satu)</label>
                <select name="unsur[]" id="unsur" multiple required>
                    <option value="U1">Unsur 1 (U1)</option>
                    <option value="U2">Unsur 2 (U2)</option>
                    <option value="U3">Unsur 3 (U3)</option>
                    <option value="U4">Unsur 4 (U4)</option>
                    <option value="U5">Unsur 5 (U5)</option>
                    <option value="U6">Unsur 6 (U6)</option>
                    <option value="U7">Unsur 7 (U7)</option>
                    <option value="U8">Unsur 8 (U8)</option>
                    <option value="U9">Unsur 9 (U9)</option>
                </select>
            </div>

            <!-- Tema (Single Select) -->
            <div>
                <label class="block text-[#1a365d] text-sm font-bold mb-2">Tema / Indikator</label>
                <select name="tema" id="tema" required>
                    <option value="">-- Pilih Tema --</option>
                    <option value="Kognitif">Kognitif</option>
                    <option value="Psikomotorik">Psikomotorik</option>
                    <option value="Afektif">Afektif</option>
                </select>
            </div>
        </div>

        <div class="grid md:grid-cols-3 gap-6 mb-6">
            <!-- Nilai Teori -->
            <div>
                <label class="block text-[#1a365d] text-sm font-bold mb-2">Nilai Teori</label>
                <input type="number" id="nilai_teori" name="nilai_teori" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 calc-jpl" value="0" required>
            </div>
            
            <!-- Nilai Praktik -->
            <div>
                <label class="block text-[#1a365d] text-sm font-bold mb-2">Nilai Praktik</label>
                <input type="number" id="nilai_praktik" name="nilai_praktik" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 calc-jpl" value="0" required>
            </div>

            <!-- JPL (Otomatis) -->
            <div>
                <label class="block text-gray-500 text-sm font-bold mb-2">JPL (Teori + Praktik)</label>
                <input type="number" id="jpl" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-100 font-bold text-gray-600" value="0" readonly>
                <p class="text-[10px] text-gray-400 mt-1">*Dihitung otomatis</p>
            </div>
        </div>

        <div class="grid md:grid-cols-3 gap-6 mb-8">
            <div>
                <label class="block text-[#1a365d] text-sm font-bold mb-2">Ambang Batas</label>
                <input type="number" step="0.01" name="ambang_batas" class="w-full px-4 py-2.5 rounded-xl border border-gray-200" required>
            </div>
            <div>
                <label class="block text-[#1a365d] text-sm font-bold mb-2">Rentang Nilai (Min)</label>
                <input type="number" name="rentang_nilai_min" class="w-full px-4 py-2.5 rounded-xl border border-gray-200" required>
            </div>
            <div>
                <label class="block text-[#1a365d] text-sm font-bold mb-2">Rentang Nilai (Max)</label>
                <input type="number" name="rentang_nilai_max" class="w-full px-4 py-2.5 rounded-xl border border-gray-200" required>
            </div>
        </div>

        <button type="submit" class="bg-[#1a365d] hover:bg-[#1ba1e2] text-white font-bold py-3 px-10 rounded-xl transition-all shadow-md">
            Simpan Evaluasi Materi
        </button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi Choices.js untuk Select
        const selectPelatihan = new Choices('#pelatihan_id', { searchEnabled: true, placeholderValue: 'Cari Pelatihan...' });
        const selectTema = new Choices('#tema', { searchEnabled: false });
        const selectUnsur = new Choices('#unsur', { 
            removeItemButton: true, // Memunculkan tombol 'x' untuk menghapus item yang dipilih
            placeholderValue: 'Pilih unsur U1-U9',
            searchPlaceholderValue: 'Cari unsur...'
        });

        // Logika Perhitungan Otomatis JPL
        const inputTeori = document.getElementById('nilai_teori');
        const inputPraktik = document.getElementById('nilai_praktik');
        const inputJpl = document.getElementById('jpl');

        function hitungJPL() {
            // Konversi ke angka, jika kosong jadikan 0
            const teori = parseInt(inputTeori.value) || 0;
            const praktik = parseInt(inputPraktik.value) || 0;
            inputJpl.value = teori + praktik;
        }

        // Jalankan fungsi setiap kali ada input yang diketik/berubah
        inputTeori.addEventListener('input', hitungJPL);
        inputPraktik.addEventListener('input', hitungJPL);
    });
</script>
@endsection