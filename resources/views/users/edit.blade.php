@extends('layouts.admin')
@section('title', 'Edit Akun')

@section('content')
<!-- Header -->
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-2xl md:text-3xl font-extrabold text-[#1a365d]">Edit Data Akun</h2>
        <p class="text-gray-500 text-sm mt-1">Perbarui informasi pengguna: <b>{{ $user->name }}</b></p>
    </div>
    <a href="{{ route('users.index') }}" class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-bold py-2.5 px-6 rounded-xl transition-colors flex items-center gap-2 justify-center shadow-sm">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Kembali
    </a>
</div>

<!-- Alert Error -->
@if($errors->any())
<div class="bg-red-50 text-red-500 p-4 rounded-xl mb-6 border border-red-100 flex items-start gap-3">
    <svg class="w-6 h-6 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
    <ul class="list-disc list-inside text-sm font-medium">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<!-- Form Card (Full Width) -->
<div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 md:p-10 w-full">
    <form action="{{ route('users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <!-- Grid Nama dan Email -->
        <div class="grid md:grid-cols-2 gap-6 mb-6">
            <!-- Nama -->
            <div>
                <label class="block text-[#1a365d] text-sm font-bold mb-2">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full px-5 py-4 rounded-xl border border-gray-200 focus:outline-none focus:border-[#1ba1e2] focus:ring-2 focus:ring-[#1ba1e2]/20 transition-all font-medium text-gray-700 bg-gray-50 focus:bg-white" required>
            </div>
            
            <!-- Email -->
            <div>
                <label class="block text-[#1a365d] text-sm font-bold mb-2">Alamat Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full px-5 py-4 rounded-xl border border-gray-200 focus:outline-none focus:border-[#1ba1e2] focus:ring-2 focus:ring-[#1ba1e2]/20 transition-all font-medium text-gray-700 bg-gray-50 focus:bg-white" required>
            </div>
        </div>
        
        <!-- Input Password Baru (Show/Hide) -->
        <div class="mb-8 w-full" x-data="{ showPassword: false }">
            <label class="block text-[#1a365d] text-sm font-bold mb-2">Password Baru <span class="text-gray-400 font-normal italic">(Opsional)</span></label>
            <div class="relative">
                <input :type="showPassword ? 'text' : 'password'" name="password" class="w-full px-5 py-4 pr-14 rounded-xl border border-gray-200 focus:outline-none focus:border-[#1ba1e2] focus:ring-2 focus:ring-[#1ba1e2]/20 transition-all font-medium text-gray-700 bg-gray-50 focus:bg-white" placeholder="Biarkan kosong jika tidak ingin mengubah password">
                
                <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-[#1ba1e2] focus:outline-none transition-colors">
                    <svg x-show="!showPassword" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    <svg x-show="showPassword" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                </button>
            </div>
            <p class="text-xs text-gray-500 mt-2 ml-1">Minimal 6 karakter jika ingin mengganti password.</p>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 border-t border-gray-100 pt-8 mt-4">
            <button type="submit" class="bg-gradient-to-r from-[#1ba1e2] to-[#5bc0de] hover:from-[#1a365d] hover:to-[#1ba1e2] text-white font-bold py-4 px-12 rounded-xl transition-all shadow-lg transform hover:-translate-y-1 w-full sm:w-auto text-center">
                Perbarui Akun
            </button>
            <a href="{{ route('users.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-4 px-12 rounded-xl transition-colors text-center w-full sm:w-auto">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection