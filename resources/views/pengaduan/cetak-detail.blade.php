<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pengaduan - {{ $pengaduan->nama_lengkap }}</title>
    <style>
        @page { size: A4 portrait; margin: 15mm; }
        body { background: #f3f4f6; font-family: 'Times New Roman', Times, serif; color: #000; }
        .a4-container {
            width: 210mm;
            min-height: 297mm;
            margin: 2rem auto;
            background: white;
            padding: 20mm;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }
        @media print {
            body { background: white; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .a4-container { margin: 0; box-shadow: none; border: none; padding: 0; width: 100%; }
            .no-print { display: none !important; }
        }
        .kop-surat { display: flex; align-items: center; border-bottom: 3px solid #000; padding-bottom: 15px; margin-bottom: 25px; gap: 15px; }
        .logo { width: 75px; height: 75px; object-fit: contain; }
        .instansi { font-size: 16pt; font-weight: bold; color: #0056b3; margin: 0; text-transform: uppercase; font-family: Arial, sans-serif;}
        .alamat { font-size: 10pt; margin-top: 3px; font-family: Arial, sans-serif;}
        .title { text-align: center; font-size: 14pt; font-weight: bold; text-decoration: underline; text-transform: uppercase; margin-bottom: 30px; }
        table.detail-table { width: 100%; border-collapse: collapse; font-size: 11pt; margin-bottom: 30px; }
        table.detail-table th, table.detail-table td { border: 1px solid #000; padding: 10px 12px; vertical-align: top; }
        table.detail-table th { width: 30%; background-color: #f9f9f9; text-align: left; }
        .signature-area { margin-top: 50px; float: right; width: 250px; text-align: center; }
        .btn-print { background-color: #1ba1e2; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-family: sans-serif; font-weight: bold; margin: 20px auto; display: block; text-align: center; width: max-content;}
    </style>
</head>
<body>

    <div class="no-print">
        <button onclick="window.print()" class="btn-print">Cetak</button>
    </div>

    <div class="a4-container">
        <!-- Kop Surat -->
        <div class="kop-surat">
            <img src="{{ asset('storage/ikon.png') }}" alt="Logo FITC" class="logo">
            <div>
                <h1 class="instansi">Flash Inspire Training Center (FIT-C)</h1>
                <p class="alamat">Dusun Sobontoro RT 03 RW 02 Desa Watudandang, Kec. Prambon, Kab. Nganjuk, Jawa Timur</p>
            </div>
        </div>

        <div class="title">Berita Acara Tindak Lanjut Pengaduan</div>

        <!-- Tabel Detail -->
        <table class="detail-table">
            <tr>
                <th>Nomor Laporan / ID</th>
                <td><strong>#SPL-{{ str_pad($pengaduan->id, 5, '0', STR_PAD_LEFT) }}</strong></td>
            </tr>
            <tr>
                <th>Waktu Laporan Masuk</th>
                <td>{{ $pengaduan->created_at->translatedFormat('l, d F Y - H:i') }} WIB</td>
            </tr>
            <tr>
                <th>Nama Pelapor</th>
                <td>{{ $pengaduan->nama_lengkap }}</td>
            </tr>
            <tr>
                <th>Kontak Info</th>
                <td>WA: {{ $pengaduan->no_whatsapp }}<br>Email: {{ $pengaduan->email }}</td>
            </tr>
            <tr>
                <th>Tempat Kejadian</th>
                <td>{{ $pengaduan->tempat_kejadian }}</td>
            </tr>
            <tr>
                <th>Isi Pengaduan</th>
                <td style="text-align: justify;">{{ nl2br(e($pengaduan->isi_pengaduan)) }}</td>
            </tr>
            <tr>
                <th>Kritik & Saran Tambahan</th>
                <td style="text-align: justify; font-style: italic;">
                    {{ $pengaduan->kritik_saran ? nl2br(e($pengaduan->kritik_saran)) : '-' }}
                </td>
            </tr>
            <tr>
                <th>Status Penanganan</th>
                <td><strong>{{ strtoupper($pengaduan->status) }}</strong></td>
            </tr>
            <tr>
                <th style="background-color: #e8f4fd;">Tanggapan / Solusi Admin</th>
                <td style="background-color: #f7fbff; text-align: justify;">
                    @if($pengaduan->jawaban_admin)
                        {{ nl2br(e($pengaduan->jawaban_admin)) }}
                        
                        <div style="margin-top: 15px; font-size: 9pt; color: #555;">
                            <em>*Tanggapan diberikan pada: {{ $pengaduan->tanggal_dijawab ? \Carbon\Carbon::parse($pengaduan->tanggal_dijawab)->translatedFormat('d F Y, H:i') : '-' }}</em>
                        </div>
                    @else
                        <em>Belum ada tanggapan atau solusi dari pihak admin.</em>
                    @endif
                </td>
            </tr>
        </table>

        <!-- Tanda Tangan -->
     <!-- Tanda Tangan QR Code Direktur -->
        <div class="signature-area">
            <p style="margin-bottom: 5px;">Kediri, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <p style="margin-bottom: 10px;">Mengetahui,<br>Direktur FIT-C</p>
            
            <!-- Tempat QR Code -->
            <div style="margin: 0 auto; width: 80px; height: 80px;">
                
                <!-- OPSI 1: Generate QR Code Otomatis (Instan) menggunakan API gratis -->
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode('Ditandatangani secara elektronik oleh Direktur FIT-C  dr. WINDY ARY WIJAYA, Sp.An-Ti untuk Laporan #SPL-' . $pengaduan->id) }}" alt="QR TTD Direktur" style="width: 100%; height: 100%;">

                

            </div>

            <p style="font-weight: bold; text-decoration: underline; margin-bottom: 0; margin-top: 10px;">dr. WINDY ARY WIJAYA, Sp.An-Ti</p>
        </div>
    </div>

</body>
</html>