<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validasi Sertifikat FIT-C</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Plus Jakarta Sans', sans-serif; } </style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">

    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
        <!-- Header -->
        <div class="bg-[#1a365d] p-6 text-center relative overflow-hidden">
            <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
            <div class="relative z-10 bg-white/10 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-3 backdrop-blur-sm border border-white/20">
                <!-- Ikon Ceklis Hijau -->
                <svg class="w-10 h-10 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h1 class="relative z-10 text-2xl font-extrabold text-white">Sertifikat Valid</h1>
            <p class="relative z-10 text-blue-200 text-sm mt-1">Telah diverifikasi oleh sistem FIT-C</p>
        </div>

        <!-- Body Detail -->
        <div class="p-6">
            <h2 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Informasi Sertifikat</h2>

            <div class="space-y-4">
                <div class="border-b border-gray-100 pb-3">
                    <p class="text-xs text-gray-500">Nomor Sertifikat</p>
                    <p class="font-semibold text-gray-800">{{ $nomorSertifikat }}</p>
                </div>
                <div class="border-b border-gray-100 pb-3">
                    <p class="text-xs text-gray-500">Nama Peserta</p>
                    <p class="font-semibold text-gray-800">{{ $registrasi->nama_lengkap }}</p>
                    <p class="text-xs text-gray-500">{{ $registrasi->instansi }}</p>
                </div>
                <div class="border-b border-gray-100 pb-3">
                    <p class="text-xs text-gray-500">Nama Pelatihan</p>
                    <p class="font-semibold text-gray-800">{{ optional($event->pelatihan)->nama_pelatihan ?? $event->nama_event }}</p>
                </div>
                <div class="border-b border-gray-100 pb-3">
                    <p class="text-xs text-gray-500">Tanggal Pelaksanaan</p>
                    <p class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($event->tanggal_mulai)->format('d F Y') }} - {{ \Carbon\Carbon::parse($event->tanggal_selesai)->format('d F Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Penandatangan Dokumen</p>
                    <p class="font-bold text-[#1a365d] text-lg">dr. WINDY ARI WIJAYA, Sp.An-Ti.</p>
                    <p class="text-xs font-semibold bg-emerald-100 text-emerald-700 px-2 py-1 rounded inline-block mt-1">Direktur Utama FIT-C</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 p-4 text-center border-t border-gray-100">
            <p class="text-[11px] text-gray-500">Scan QR Code membuktikan bahwa sertifikat yang Anda pegang adalah asli dan sah diterbitkan oleh sistem Flash Inspire Training Center.</p>
        </div>
    </div>

</body>
</html>