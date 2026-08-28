@extends('layouts.admin')
@section('title', 'Manajemen Akun')

@section('content')
<!-- Header Halaman -->
<div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-end gap-4">
    <div>
        <h2 class="text-2xl md:text-3xl font-extrabold text-[#1a365d]">Daftar Akun Pengguna</h2>
        <p class="text-gray-500 text-sm mt-1">Kelola akses sistem FITC, cari, dan urutkan data.</p>
    </div>
    <a href="{{ route('users.create') }}" class="bg-[#1a365d] hover:bg-[#1ba1e2] text-white px-6 py-3 rounded-xl font-bold transition-all duration-300 shadow-lg shadow-[#1a365d]/20 transform hover:-translate-y-1 text-center whitespace-nowrap">
        + Tambah Akun Baru
    </a>
</div>

<!-- Notifikasi Sukses -->
@if(session('success'))
<div x-data="{ show: true }" x-show="show" class="bg-green-50 border border-green-200 text-green-700 p-4 mb-6 rounded-xl shadow-sm flex justify-between items-center">
    <div class="flex items-center gap-3">
        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <span class="font-bold">{{ session('success') }}</span>
    </div>
    <button @click="show = false" class="text-green-500 hover:text-green-700"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
</div>
@endif

<!-- Baris Pencarian & Filter -->
<div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6">
    <form action="{{ route('users.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center">
        
        <!-- Search Input -->
        <div class="relative flex-1 w-full">
            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}" class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#1ba1e2]/20 focus:border-[#1ba1e2] transition-colors text-sm font-medium" placeholder="Cari nama atau email...">
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
            <a href="{{ route('users.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-500 px-4 py-3 rounded-xl font-bold transition-all flex items-center" title="Reset Filter">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            </a>
            @endif
        </div>
    </form>
</div>

<!-- Container Tabel -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-slate-50 text-[#1a365d] text-sm uppercase tracking-wider border-b border-gray-100">
                    <th class="p-5 font-extrabold w-16 text-center">No</th>
                    <th class="p-5 font-extrabold">Nama Pengguna</th>
                    <th class="p-5 font-extrabold">Alamat Email</th>
                    <th class="p-5 font-extrabold">Role</th>
                    <th class="p-5 font-extrabold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                @forelse($users as $index => $user)
                <tr class="hover:bg-blue-50/50 border-b border-gray-50 transition-colors">
                    <td class="p-5 text-center font-semibold text-gray-400">
                        {{ $users->firstItem() + $index }}
                    </td>
                    <td class="p-5">
                        <p class="font-bold text-[#1a365d]">{{ $user->name }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">ID: #{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</p>
                    </td>
                    <td class="p-5 font-medium">{{ $user->email }}</td>
                    <td class="p-5">
                        <span class="bg-blue-100 text-[#1ba1e2] px-3 py-1.5 rounded-full text-xs font-bold border border-blue-200">
                            Administrator
                        </span>
                    </td>
                    <td class="p-5 text-center">
                        <!-- Aksi Selalu Muncul (Tidak di-hide) -->
                        <div class="flex items-center justify-center gap-2">
                            <!-- Tombol Edit -->
                            <a href="{{ route('users.edit', $user->id) }}" class="bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white p-2 rounded-lg transition-colors border border-amber-200 hover:border-transparent" title="Edit Data">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </a>
                            
                            <!-- Form Delete via SweetAlert2 -->
                            <form id="delete-form-{{ $user->id }}" action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmDelete('{{ $user->id }}', '{{ addslashes($user->name) }}')" class="bg-red-50 text-red-500 hover:bg-red-500 hover:text-white p-2 rounded-lg transition-colors border border-red-200 hover:border-transparent" title="Hapus Data">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-10 text-center">
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
    @if($users->hasPages())
    <div class="p-5 border-t border-gray-100 bg-gray-50">
        {{ $users->links() }}
    </div>
    @endif
</div>

<!-- Script SweetAlert2 untuk Delete -->
<script>
    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Hapus Akun?',
            html: "Anda yakin ingin menghapus akun <b>" + name + "</b> secara permanen?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            backdrop: `rgba(0,0,0,0.6)`,
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-xl px-6 py-2.5 font-bold',
                cancelButton: 'rounded-xl px-6 py-2.5 font-bold'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Eksekusi Form Submit yang sesuai dengan ID
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>
@endsection