<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keterangan Event - {{ $event->nama_event }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Pengaturan Kertas A4 */
        @page { size: A4 portrait; margin: 10mm; } /* Margin bawaan browser dikecilkan */
        body { background: #f3f4f6; font-family: 'Times New Roman', Times, serif; color: #000; }
        
        .a4-container {
            width: 210mm;
            min-height: 297mm;
            margin: 2rem auto;
            background: white;
            padding: 15mm 20mm; /* Padding di layar */
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }
        
        /* Pengaturan Cetak */
        @media print {
            body { background: white; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .a4-container { margin: 0; box-shadow: none; border: none; padding: 0 5mm; min-height: auto; width: 100%; }
            .no-print { display: none !important; }
        }

        /* Tabel Data - Dibuat lebih padat */
        table.detail-table { width: 100%; border-collapse: collapse; font-size: 11pt; margin-bottom: 5px; }
        table.detail-table td { padding: 3px 6px; vertical-align: top; line-height: 1.3; }
        table.detail-table td:first-child { font-weight: bold; width: 30%; }
        table.detail-table td:nth-child(2) { width: 2%; text-align: center; }
        
        /* Judul sub-bagian di dalam tabel */
        .sub-judul { font-weight: bold; text-decoration: underline; margin-top: 10px; margin-bottom: 2px; font-size: 11pt; }
        
        /* Mencegah TTD terpotong ke halaman baru */
        .ttd-container { page-break-inside: avoid; }
    </style>
</head>
<body>

    <!-- Tombol Aksi (Sembunyi saat diprint) -->
    <div class="max-w-[210mm] mx-auto mt-6 flex justify-between items-center no-print px-4">
        <a href="{{ route('event.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-5 py-2 rounded-lg font-sans text-sm font-bold shadow transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg> Kembali
        </a>
        <button onclick="window.print()" class="bg-[#1ba1e2] hover:bg-blue-600 text-white px-6 py-2 rounded-lg font-sans text-sm font-bold shadow transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg> Cetak Dokumen (PDF)
        </button>
    </div>

    <!-- Halaman Kertas A4 -->
    <div class="a4-container">
        
        <!-- KOP SURAT (Dibuat lebih padat) -->
        <div style="border-top: 4px solid #000; border-bottom: 3px solid #000; padding: 10px 0; display: flex; align-items: center; gap: 15px; margin-bottom: 20px;">
            <div style="width: 75px; height: 75px; flex-shrink: 0;">
                <img src="{{ asset('storage/icon.png') }}" alt="Logo FITC" style="width: 100%; height: 100%; object-fit: contain;">
            </div>
            
            <div style="font-family: Arial, Helvetica, sans-serif;">
                <h1 style="font-size: 15pt; font-weight: bold; color: #0056b3; margin: 0; text-transform: uppercase;">
                    Flash Inspire Training Center (FIT-C)
                </h1>
                <p style="font-size: 10pt; margin: 2px 0 0 0;">Dusun Sobontoro RT 03 RW 02 Desa Watudandang</p>
                <p style="font-size: 10pt; margin: 2px 0 0 0;">Kecamatan Prambon Kabupaten Nganjuk Provinsi Jawa Timur</p>
                <p style="font-size: 10pt; margin: 2px 0 0 0;">082228717819</p>
            </div>
        </div>

        <!-- JUDUL DOKUMEN -->
        <div class="text-center mb-6">
            <h2 class="font-bold underline uppercase" style="font-size: 13pt;">Surat Keterangan Penyelenggaraan Pelatihan</h2>
            <p style="font-size: 10pt; margin-top: 2px;">Nomor: FITC/{{ date('Y') }}/SKP/{{ strtoupper(substr($event->uuid, 0, 6)) }}</p>
        </div>

        <!-- ISI SURAT -->
        <div class="text-justify mb-2" style="font-size: 11pt; line-height: 1.4;">
            <p>
                Yang bertanda tangan di bawah ini, Direktur Utama Flash Inspire Training Center (FIT-C), menerangkan secara resmi bahwa kami telah menetapkan agenda program pengembangan kompetensi dengan rincian data sebagai berikut:
            </p>
        </div>

        <!-- TABEL DATA EVENT -->
        <div class="sub-judul">I. Informasi Acara Utama</div>
        <table class="detail-table">
            <tr><td>Nama Event</td><td>:</td><td>{{ $event->nama_event }}</td></tr>
            <tr><td>Master Pelatihan</td><td>:</td><td>{{ $event->pelatihan->nama_pelatihan ?? '-' }}</td></tr>
            <tr><td>Tipe / Sistem Pelatihan</td><td>:</td><td>{{ $event->tipe_pelatihan }} ({{ $event->sistem_pelatihan }})</td></tr>
            <tr><td>Jenis Pelatihan</td><td>:</td><td>{{ $event->jenis_pelatihan }}</td></tr>
            <tr><td>Instansi Penyelenggara</td><td>:</td><td>{{ $event->instansi_penyelenggara ?? 'Flash Inspire Training Center (FIT-C)' }}</td></tr>
        </table>

        <div class="sub-judul">II. Jadwal & Lokasi</div>
        <table class="detail-table">
            <tr><td>Batch / Tahun</td><td>:</td><td>Batch {{ $event->batch ?? '-' }} / {{ $event->tahun }}</td></tr>
            <tr><td>Tanggal Pelaksanaan</td><td>:</td><td>{{ \Carbon\Carbon::parse($event->tanggal_mulai)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($event->tanggal_selesai)->translatedFormat('d F Y') }}</td></tr>
            <tr><td>Waktu Presensi</td><td>:</td><td>{{ $event->waktu_presensi_mulai }} WIB - {{ $event->waktu_presensi_selesai }} WIB</td></tr>
            <tr><td>Lokasi / Tempat Acara</td><td>:</td><td>{{ $event->lokasi ?? '-' }}</td></tr>
            @if($event->sistem_pelatihan === 'Daring' || $event->sistem_pelatihan === 'Blended')
            <tr><td>Link Zoom (Daring)</td><td>:</td><td><a href="{{ $event->link_zoom }}" style="color: #2563eb; text-decoration: underline;">Tautan Tersedia</a></td></tr>
            @endif
        </table>

        <div class="sub-judul">III. Administrasi</div>
        <table class="detail-table">
            <tr><td>Bobot Nilai SKP</td><td>:</td><td>{{ $event->skp ?? '0' }} SKP</td></tr>
            <tr><td>Biaya Pelatihan</td><td>:</td><td>Rp {{ number_format($event->biaya_pelatihan ?? 0, 0, ',', '.') }}</td></tr>
            <tr><td>Akses Materi</td><td>:</td><td><a href="{{ $event->link_materi }}" style="color: #2563eb; text-decoration: underline;">{{ $event->link_materi ? 'Tautan Tersedia' : 'Belum tersedia' }}</a></td></tr>
        </table>

        <!-- PARAGRAF PENUTUP -->
        <div class="text-justify mt-4 mb-4" style="font-size: 11pt; line-height: 1.4;">
            <p>
                Demikian surat keterangan penyelenggaraan ini diterbitkan. Dokumen ini adalah bukti otentik bahwa program di atas diselenggarakan secara resmi di bawah naungan <strong>Flash Inspire Training Center (FIT-C)</strong>. Dokumen ini dapat diverifikasi keasliannya dengan melakukan pemindaian <em>(scan)</em> pada <em>QR Code</em> di bawah ini.
            </p>
        </div>

        <!-- TANDA TANGAN & QR CODE (TIDAK AKAN TERPOTONG) -->
        <div class="flex justify-end ttd-container">
            <div class="text-center w-[260px]">
                <p style="font-size: 11pt; margin-bottom: 2px;">Kediri, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                <p style="font-size: 11pt; margin-bottom: 5px;">Direktur Utama FIT-C</p>
                
                <!-- Setup URL QR Code -->
                @php
                    $urlValidasi = url('/validasi-dokumen/event/' . $event->uuid);
                    $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&margin=1&data=" . urlencode($urlValidasi);
                @endphp
                
                <!-- Wadah QR Code dengan Logo Ditengah (Diperkecil sedikit) -->
                <div class="relative inline-block p-1 border border-gray-400 rounded-md bg-white mb-1" style="width: 85px; height: 85px;">
                    <img src="{{ $qrUrl }}" alt="QR Validasi" class="w-full h-full object-contain">
                    <!-- Logo di tengah QR Code -->
                    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white p-0.5 rounded shadow-sm">
                        <img src="{{ asset('storage/icon.png') }}" class="w-5 h-5 object-contain">
                    </div>
                </div>
                
                <p style="font-size: 11pt; font-weight: bold; text-decoration: underline; margin-top: 2px;">dr. WINDY ARY WIJAYA, Sp.An-Ti</p>
            </div>
        </div>

    </div>
</body>
</html>