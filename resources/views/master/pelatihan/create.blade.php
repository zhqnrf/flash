@extends('layouts.admin')
@section('title', 'Tambah Pelatihan')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h2 class="text-2xl md:text-3xl font-extrabold text-[#1a365d]">Tambah Pelatihan Baru</h2>
    <a href="{{ route('master.pelatihan.index') }}" class="bg-white border border-gray-200 text-gray-700 font-bold py-2.5 px-6 rounded-xl shadow-sm">Kembali</a>
</div>

<div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 md:p-10 w-full">
    <form action="{{ route('master.pelatihan.store') }}" method="POST">
        @csrf
        <div class="grid md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-[#1a365d] text-sm font-bold mb-2">Nomor Pelatihan / SK</label>
                <input type="text" name="nomor" value="{{ old('nomor') }}" class="w-full px-5 py-4 rounded-xl border border-gray-200 focus:outline-none focus:border-[#1ba1e2] bg-gray-50 focus:bg-white" placeholder="Contoh: 589/H/A.I/..." required>
            </div>
            <div>
                <label class="block text-[#1a365d] text-sm font-bold mb-2">Nama Pelatihan</label>
                <input type="text" name="nama_pelatihan" value="{{ old('nama_pelatihan') }}" class="w-full px-5 py-4 rounded-xl border border-gray-200 focus:outline-none focus:border-[#1ba1e2] bg-gray-50 focus:bg-white" placeholder="Contoh: BTCLS / ACLS For Nurse" required>
            </div>
        </div>
        <div class="border-t border-gray-100 pt-6">
            <button type="submit" class="bg-gradient-to-r from-[#1a365d] to-[#1ba1e2] text-white font-bold py-4 px-12 rounded-xl shadow-lg">Simpan Data</button>
        </div>
    </form>
</div>
@endsection