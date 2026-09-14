<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Laporan Pengaduan - SIMPEL FIT-C</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page { size: A4 landscape; margin: 10mm; }
        body { background: #f3f4f6; font-family: 'Times New Roman', Times, serif; color: #000; }
        .a4-container {
            width: 297mm;
            min-height: 210mm;
            margin: 2rem auto;
            background: white;
            padding: 15mm 20mm;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }
        @media print {
            body { background: white; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .a4-container { margin: 0; box-shadow: none; border: none; padding: 0; width: 100%; }
            .no-print { display: none !important; }
        }
        table.data-table { width: 100%; border-collapse: collapse; font-size: 10pt; margin-top: 15px; }
        table.data-table th, table.data-table td { border: 1px solid #000; padding: 6px 8px; vertical-align: top; }
        table.data-table th { background-color: #f2f2f2; text-align: center; }
    </style>
</head>
<body>

    <!-- Tombol Aksi -->
    <div class="max-w-[297mm] mx-auto mt-6 flex justify-between items-center no-print px-4">
        <a href="{{ route('pengaduan.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-5 py-2 rounded-lg font-sans text-sm font-bold shadow transition-colors">Kembali</a>
        <button onclick="window.print()" class="bg-[#1ba1e2] hover:bg-blue-600 text-white px-6 py-2 rounded-lg font-sans text-sm font-bold shadow transition-colors">Cetak / Simpan PDF</button>
    </div>

    <!-- Kertas A4 Landscape -->
    <div class="a4-container">
        <!-- Kop Surat -->
        <div style="border-top: 4px solid #000; border-bottom: 3px solid #000; padding: 10px 0; display: flex; align-items: center; gap: 15px; margin-bottom: 20px;">
            <div style="width: 65px; height: 65px; flex-shrink: 0;">
                <img src="{{ asset('storage/ikon.png') }}" alt="Logo FITC" style="width: 100%; height: 100%; object-fit: contain;">
            </div>
            <div style="font-family: Arial, Helvetica, sans-serif;">
                <h1 style="font-size: 14pt; font-weight: bold; color: #0056b3; margin: 0; text-transform: uppercase;">Flash Inspire Training Center (FIT-C)</h1>
                <p style="font-size: 9pt; margin: 2px 0 0 0;">Dusun Sobontoro RT 03 RW 02 Desa Watudandang, Kec. Prambon, Kab. Nganjuk, Jawa Timur</p>
            </div>
        </div>

        <div class="text-center mb-6">
            <h2 class="font-bold uppercase" style="font-size: 13pt;">Laporan Rekapitulasi Pengaduan Pelatihan (SIMPEL)</h2>
            <p style="font-size: 10pt; margin-top: 2px;">Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}</p>
        </div>

        <!-- Tabel Data -->
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Nama Pelapor</th>
                    <th>Kontak (WA / Email)</th>
                    <th>Tempat Kejadian</th>
                    <th>Isi Pengaduan</th>
                    <th>Status</th>
                    <th>Tanggapan Admin</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengaduans as $index => $p)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $p->created_at->format('d/m/Y H:i') }}</td>
                    <td><strong>{{ $p->nama_lengkap }}</strong></td>
                    <td>{{ $p->no_whatsapp }}<br><span style="font-size: 8pt; color: #555;">{{ $p->email }}</span></td>
                    <td>{{ $p->tempat_kejadian }}</td>
                    <td>{{ $p->isi_pengaduan }}</td>
                    <td class="text-center"><strong>{{ $p->status }}</strong></td>
                    <td>{{ $p->jawaban_admin ?? 'Belum ditanggapi' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4">Tidak ada data pengaduan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Tanda Tangan -->
        <div class="flex justify-end mt-12">
            <div class="text-center w-[250px]">
                <p style="font-size: 10pt; margin-bottom: 2px;">Kediri, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                <p style="font-size: 10pt; margin-bottom: 50px;">Admin Layanan SIMPEL</p>
                <p style="font-size: 10pt; font-weight: bold; text-decoration: underline;">Tim Pelayanan FIT-C</p>
            </div>
        </div>
    </div>
</body>
</html>