<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validasi Dokumen FIT-C</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Plus Jakarta Sans', sans-serif; } </style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">

    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl overflow-hidden">
        <!-- Header -->
        <div class="bg-[#1a365d] p-6 text-center">
            <div class="bg-white/10 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-3">
                <!-- Ikon Ceklis Hijau -->
                <svg class="w-10 h-10 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h1 class="text-2xl font-extrabold text-white">Dokumen Valid</h1>
            <p class="text-blue-200 text-sm mt-1">Telah diverifikasi oleh sistem FIT-C</p>
        </div>

        <!-- Body Detail -->
        <div class="p-6">
            <h2 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Informasi Dokumen</h2>
            
            <div class="space-y-4">
                <div class="border-b border-gray-100 pb-3">
                    <p class="text-xs text-gray-500">Jenis Laporan</p>
                    <p class="font-semibold text-gray-800">Laporan Survey Kepuasan Masyarakat (IKM)</p>
                </div>
                <div class="border-b border-gray-100 pb-3">
                    <p class="text-xs text-gray-500">Instansi / Unit Pelayanan</p>
                    <p class="font-semibold text-gray-800">Flash Inspire Training Center (FIT-C)</p>
                </div>
                <div class="border-b border-gray-100 pb-3">
                    <p class="text-xs text-gray-500">Periode Laporan</p>
                    <p class="font-semibold text-gray-800">{{ $namaBulan }} {{ $tahun }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Penandatangan Laporan</p>
                    <p class="font-bold text-[#1a365d] text-lg">dr. WINDY ARI WIJAYA, Sp.An-Ti.</p>
                    <p class="text-xs font-semibold bg-emerald-100 text-emerald-700 px-2 py-1 rounded inline-block mt-1">Direktur FIT-C</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 p-4 text-center border-t border-gray-100">
            <p class="text-xs text-gray-400">Scan QR Code ini membuktikan bahwa dokumen cetak yang Anda pegang adalah asli dan sah dikeluarkan oleh sistem Flash Inspire Training Center.</p>
        </div>
    </div>

</body>
</html>