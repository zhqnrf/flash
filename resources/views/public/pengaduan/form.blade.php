<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaduan Pelatihan - Flash Inspire Training Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-gradient-to-br from-slate-900 via-[#1a365d] to-slate-900 min-h-screen py-10 px-4 flex items-center justify-center">

    <div class="max-w-2xl w-full bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
        <!-- Header Form -->
        <div class="bg-[#1a365d] p-8 text-white relative">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-bl-full pointer-events-none"></div>
            <span class="bg-[#1ba1e2] text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-widest">SIMPEL FIT-C</span>
            <h1 class="text-2xl sm:text-3xl font-extrabold mt-3">Pengaduan Pelatihan</h1>
            <p class="text-blue-200 text-xs sm:text-sm mt-1 leading-relaxed">
                Sistem Informasi Pengaduan Pelayanan Pelatihan. Silakan sampaikan pengaduan, kritik, dan saran Anda untuk membantu meningkatkan kualitas layanan kami.
            </p>
        </div>

        @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Terkirim!',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#1a365d',
                    customClass: { popup: 'rounded-2xl' }
                });
            });
        </script>
        @endif

        <!-- Form Body -->
        <form action="{{ route('pengaduan.store') }}" method="POST" class="p-6 sm:p-8 space-y-6">
            @csrf

            <!-- Info Akun Simulasi -->
            <div class="bg-gray-50 p-4 rounded-2xl border border-gray-200 flex items-center justify-between text-xs sm:text-sm">
                <div>
                    <p class="text-gray-500">Mengirim sebagai:</p>
                    <p class="font-bold text-gray-800" id="user-email-display">pengguna@umum.com</p>
                </div>
                <span class="text-blue-600 font-semibold cursor-pointer hover:underline" onclick="gantiAkun()">Ganti akun</span>
            </div>

            <!-- Email Aktif -->
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Email Aktif <span class="text-red-500">*</span></label>
                <input type="email" name="email" required value="{{ old('email') }}" placeholder="nama@email.com" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#1ba1e2] text-sm font-medium">
                @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Nama Lengkap -->
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                <p class="text-[11px] text-gray-400 mb-1">Kerahasiaan nama Anda akan kami jaga dan digunakan untuk tindak lanjut.</p>
                <input type="text" name="nama_lengkap" required value="{{ old('nama_lengkap') }}" placeholder="Masukkan nama lengkap Anda" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#1ba1e2] text-sm font-medium">
                @error('nama_lengkap') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- No Whatsapp -->
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nomor Whatsapp Aktif <span class="text-red-500">*</span></label>
                <p class="text-[11px] text-gray-400 mb-1">Digunakan untuk mengirimkan balasan konfirmasi penanganan.</p>
                <input type="text" name="no_whatsapp" required value="{{ old('no_whatsapp') }}" placeholder="Contoh: 6281234567890" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#1ba1e2] text-sm font-medium">
                @error('no_whatsapp') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Tempat Kejadian -->
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Tempat Kejadian <span class="text-red-500">*</span></label>
                <input type="text" name="tempat_kejadian" required value="{{ old('tempat_kejadian') }}" placeholder="Misal: Sesi Pelatihan BHD / Ruang Kelas / Admin Pendaftaran" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#1ba1e2] text-sm font-medium">
                @error('tempat_kejadian') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Isi Pengaduan -->
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Isi Pengaduan <span class="text-red-500">*</span></label>
                <textarea name="isi_pengaduan" rows="4" required placeholder="Jelaskan detail pengaduan atau masalah yang Anda alami..." class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#1ba1e2] text-sm font-medium">{{ old('isi_pengaduan') }}</textarea>
                @error('isi_pengaduan') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Kritik dan Saran -->
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Kritik dan Saran</label>
                <textarea name="kritik_saran" rows="3" placeholder="Sampaikan saran membangun untuk kemajuan lembaga..." class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#1ba1e2] text-sm font-medium">{{ old('kritik_saran') }}</textarea>
            </div>

            <!-- Tombol Kirim -->
            <button type="submit" class="w-full py-4 bg-gradient-to-r from-[#1a365d] to-[#1ba1e2] hover:opacity-95 text-white font-bold rounded-xl shadow-lg transition-all text-base">
                Kirim Pengaduan
            </button>
        </form>

        <!-- Footer Card -->
        <div class="bg-gray-50 p-6 text-center border-t border-gray-100 text-gray-500 text-xs leading-relaxed">
            <p class="font-bold text-[#1a365d]">=== Terima Kasih Telah Mengisi Form Pengaduan Pelatihan dari Flash Inspire Training Center ===</p>
            <p class="mt-1">Masukan, kritik, serta saran dari Anda sangat membantu kami untuk terus memperbaiki kualitas pelayanan pelatihan kami menjadi semakin lebih baik.</p>
        </div>
    </div>

    <script>
        function gantiAkun() {
            Swal.fire({
                title: 'Ganti Akun Email',
                input: 'email',
                inputPlaceholder: 'Masukkan email baru Anda',
                confirmButtonColor: '#1a365d',
                customClass: { popup: 'rounded-2xl' }
            }).then((result) => {
                if (result.value) {
                    document.getElementById('user-email-display').innerText = result.value;
                }
            });
        }
    </script>
</body>
</html>