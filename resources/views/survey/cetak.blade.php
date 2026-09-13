<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Survey Kepuasan Masyarakat</title>
    <!-- Script HTML2PDF CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        /* Setup Dasar Kertas A4 */
        body { 
            font-family: Arial, sans-serif; 
            font-size: 10px; /* Diperkecil agar lebih padat seperti dokumen asli */
            color: #000; 
            background: #525659; 
            margin: 0; 
            padding: 20px 0; 
            text-align: center; 
        }
        
        #document-wrapper { 
            background: white; 
            width: 210mm; 
            margin: 0 auto; 
            text-align: left; 
            box-shadow: 0 0 10px rgba(0,0,0,0.5); 
        }
        
        .page { 
            padding: 10mm 15mm; 
            min-height: 297mm; 
            box-sizing: border-box; 
            position: relative; 
        }
        
        /* Utility Classes */
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }
        
        /* ========================================= */
        /* STYLE HALAMAN 1 (MATRIKS)                 */
        /* ========================================= */
        .table-main { width: 100%; border-collapse: collapse; border: 2px solid #000; }
        /* Padding diperkecil agar tabel tidak terlalu lebar ke bawah */
        .table-main td, .table-main th { border: 1px solid #000; padding: 3px 5px; vertical-align: middle; }
        
        /* Style Khusus Bagian Bawah Halaman 1 */
        .table-keterangan { width: 100%; font-size: 9px; }
        .table-keterangan td { border: none; padding: 1px 2px; vertical-align: top; }
        
        .table-unsur-kanan { width: 100%; border-collapse: collapse; }
        .table-unsur-kanan th, .table-unsur-kanan td { border: 1px solid #000; padding: 3px; text-align: center; font-size: 9px; }
        .table-unsur-kanan td:nth-child(2) { text-align: left; }

        /* ========================================= */
        /* STYLE HALAMAN 2 (POSTER IKM)              */
        /* ========================================= */
        .page-2-border { border: 4px solid #000; height: 265mm; padding: 25px; box-sizing: border-box; }
        .header-page-2 { font-size: 16px; font-weight: bold; line-height: 1.3; }
        
        .box-ikm { border: 3px solid #000; text-align: center; height: 190px; display: flex; flex-direction: column; }
        .box-ikm .title { border-bottom: 3px solid #000; font-weight: bold; padding: 5px; font-size: 13px; }
        .box-ikm .nilai-besar { font-size: 80px; font-weight: bold; margin: auto 0; line-height: 1; }
        .box-ikm .mutu { font-size: 18px; margin-bottom: 15px; }
        
        .box-responden { border: 3px solid #000; height: 190px; display: flex; flex-direction: column; }
        .box-responden .title { border-bottom: 3px solid #000; font-weight: bold; padding: 5px; text-align: center; font-size: 13px; }
        .box-responden table { width: 100%; font-size: 11px; margin-top: 10px; border-collapse: collapse; }
        .box-responden table td { padding: 2px 10px; vertical-align: top; }

        .signature { margin-top: 50px; text-align: center; float: right; width: 250px; position: relative; }
        .signature-logo { position: absolute; top: 10px; left: 50%; transform: translateX(-50%); width: 120px; opacity: 0.8; z-index: 1; }
        .signature-content { position: relative; z-index: 2; }

        #action-buttons { text-align: center; margin-bottom: 20px; }

        /* ========================================= */
        /* CSS KHUSUS PRINT (Menyembunyikan Tombol)  */
        /* ========================================= */
        @media print {
            #action-buttons { display: none !important; }
            body { background: white !important; padding: 0 !important; margin: 0 !important; }
            #document-wrapper { box-shadow: none !important; margin: 0 !important; width: 100% !important; }
            .page { padding: 0 !important; min-height: auto !important; page-break-after: always; }
        }
    </style>
</head>
<body>

    <!-- Tombol Print/Download -->
    <div id="action-buttons">
        <button onclick="downloadPDF()" style="padding: 10px 20px; background: #1a365d; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 14px;">⬇️ Download PDF</button>
        <button onclick="window.print()" style="padding: 10px 20px; background: #059669; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 14px; margin-left: 10px;">🖨️ Print Langsung</button>
    </div>

    <!-- Container Utama Kertas -->
    <div id="document-wrapper">
        
        @php
            $ikm_raw = $ikmTertimbang * 25;
            $ikm_page1 = number_format($ikm_raw, 3, '.', '');
            $ikm_page2 = number_format($ikm_raw, 2, '.', '');
        @endphp

        <!-- ========================================== -->
        <!-- HALAMAN 1: MATRIKS & KETERANGAN            -->
        <!-- ========================================== -->
        <div class="page">
            <table class="table-main">
                <!-- KOP SURAT -->
                <tr>
                    <td colspan="2" style="text-align: center; border-right: none; padding: 10px;">
                        <!-- Pastikan URL Logo Valid -->
                        <img src="{{ asset('storage/ikon.png') }}" alt="Logo" style="width: 70px;">
                    </td>
                    <td colspan="9" style="text-align: center; border-left: none; padding: 10px;">
                        <div class="bold" style="font-size: 13px; line-height: 1.3;">
                            LAPORAN HASIL PENGOLAHAN SURVEY KEPUASAN MASYARAKAT<br>
                            PER RESPONDEN DAN PER UNSUR PELAYANAN<br>
                            OLEH FLASH INSPIRE TRAINING CENTER PADA TAHUN {{ $periode }}
                        </div>
                    </td>
                </tr>
                
                <!-- INFO UNIT PELAYANAN -->
                <tr>
                    <td colspan="2" class="bold text-left">Unit Pelayanan</td>
                    <td colspan="9" class="text-left">Flash Inspire Training Center (FIT-C)</td>
                </tr>
                <tr>
                    <td colspan="2" class="bold text-left">Alamat</td>
                    <td colspan="9" class="text-left">
                        Dusun Sobontoro RT 03 RW 02 Desa Watudandang<br>
                        Kecamatan Prambon Kabupaten Nganjuk Provinsi Jawa Timur
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="bold text-left">Tlp/Fax.</td>
                    <td colspan="9" class="text-left">082228717819</td>
                </tr>

                <!-- HEADER TABEL NILAI -->
                <tr class="bold text-center bg-gray-50">
                    <td rowspan="2" style="width: 6%;">NO.<br>RESP</td>
                    <td colspan="9">NILAI UNSUR PELAYANAN</td>
                    <td rowspan="2" style="width: 10%;"></td>
                </tr>
                <tr class="bold text-center bg-gray-50">
                    @foreach($unsurs as $u) <td style="width: 9%;">{{ $u->kode }}</td> @endforeach
                </tr>
                <tr class="bold text-center bg-gray-50">
                    <td>1</td>
                    @foreach($unsurs as $i => $u) <td>{{ $i + 2 }}</td> @endforeach
                    <td></td> <!-- Hapus angka 11 di sini -->
                </tr>

                <!-- DATA RESPONDEN -->
                @foreach($matriks as $baris)
                <tr class="text-center">
                    <td class="bold">{{ $baris['no'] }}</td>
                    @foreach($unsurs as $u) <td>{{ $baris[$u->kode] ?? '-' }}</td> @endforeach
                    <td></td>
                </tr>
                @endforeach
                
                <!-- SUMMARY ROWS -->
                <tr class="text-center">
                    <td class="bold text-left">ΣNilai/<br>Unsur</td>
                    @foreach($rekapUnsur as $r) <td>{{ $unsurs->count() > 0 ? $r['nrr'] * $totalResponden : 0 }}</td> @endforeach
                    <td></td>
                </tr>
                <tr class="text-center">
                    <td class="bold text-left">NRR /<br>Unsur</td>
                    @foreach($rekapUnsur as $r) <td>{{ number_format($r['nrr'], 3, '.', '') }}</td> @endforeach
                    <td></td>
                </tr>
                <tr class="text-center">
                    <td class="bold text-left" style="font-size: 9px;">NRR/<br>tertbg/<br>Unsur</td>
                    @foreach($rekapUnsur as $r) <td>{{ number_format($r['nrr_tertimbang'], 3, '.', '') }}</td> @endforeach
                    <td class="bold text-left" style="vertical-align: bottom;">*)<br>{{ number_format($ikmTertimbang, 3, '.', '') }}</td>
                </tr>
                <tr>
                    <td colspan="10" class="bold text-left bg-gray-50">IKM Unit Pelayanan</td>
                    <td class="bold text-left bg-gray-50" style="vertical-align: bottom;">**)<br>{{ $ikm_page1 }}</td>
                </tr>

                <!-- KETERANGAN & TABEL KANAN -->
                <tr>
                    <td colspan="5" style="vertical-align: top; padding: 10px;">
                        <div class="bold" style="text-decoration: underline; margin-bottom: 3px;">Keterangan :</div>
                        <table class="table-keterangan">
                            <tr><td width="30%">- U1 s.d. U9</td><td width="5%">=</td><td>Unsur-Unsur pelayanan</td></tr>
                            <tr><td>- NRR</td><td>=</td><td>Nilai rata-rata</td></tr>
                            <tr><td>- IKM</td><td>=</td><td>Indeks Kepuasan Masyarakat</td></tr>
                            <tr><td>- *)</td><td>=</td><td>Jumlah NRR IKM tertimbang</td></tr>
                            <tr><td>- **)</td><td>=</td><td>Jumlah NRR Tertimbang x 25</td></tr>
                            <tr><td>NRR Per Unsur</td><td>=</td><td>Jumlah nilai per unsur dibagi<br>Jumlah kuesioner yang terisi</td></tr>
                            <tr><td>NRR tertimbang<br>per unsur</td><td>=</td><td>NRR per unsur x {{ $bobot }}</td></tr>
                        </table>
                        
                        <div style="border-top: 2px solid #000; border-bottom: 2px solid #000; padding: 5px 0; margin: 10px 0; display: flex; justify-content: space-between; align-items: center;">
                            <span class="bold" style="font-size: 11px;">SKM UNIT PELAYANAN :</span>
                            <span class="bold" style="font-size: 15px;">{{ $ikm_page2 }}</span>
                        </div>
                        
                        <div class="bold" style="text-decoration: underline; margin-bottom: 3px;">Mutu Pelayanan :</div>
                        <table class="table-keterangan">
                            <tr><td width="40%"><b>A</b> (Sangat Baik)</td><td width="5%">:</td><td>88,31 - 100,00</td></tr>
                            <tr><td><b>B</b> (Baik)</td><td>:</td><td>76,61 - 88,30</td></tr>
                            <tr><td><b>C</b> (Kurang Baik)</td><td>:</td><td>65,00 - 76,60</td></tr>
                            <tr><td><b>D</b> (Tidak Baik)</td><td>:</td><td>25,00 - 64,99</td></tr>
                        </table>
                    </td>
                    
                    <td colspan="6" style="vertical-align: top; padding: 0;">
                        <table class="table-unsur-kanan" style="border-style: hidden;">
                            <tr class="bold bg-gray-50">
                                <th width="15%">No.</th>
                                <th>UNSUR PELAYANAN</th>
                                <th width="25%">NILAI RATA-RATA</th>
                            </tr>
                            @foreach($rekapUnsur as $r)
                            <tr>
                                <td class="bold">{{ $r['kode'] }}</td>
                                <td>{{ $r['nama_unsur'] }}</td>
                                <td>{{ number_format($r['nrr'], 3, '.', '') }}</td>
                            </tr>
                            @endforeach
                        </table>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- PEMISAH HALAMAN PDF -->
        <div class="html2pdf__page-break"></div>

        <!-- ========================================== -->
        <!-- HALAMAN 2: POSTER IKM                      -->
        <!-- ========================================== -->
        <div class="page">
            <div class="page-2-border">
                
                <!-- Header Halaman 2 -->
                <table style="width: 100%; margin-bottom: 10px;">
                    <tr>
                        <td width="20%" class="text-center">
                            <img src="{{ asset('storage/ikon.png') }}" alt="Logo" style="width: 90px;">
                        </td>
                        <td width="80%" class="text-center header-page-2">
                            INDEKS KEPUASAN MASYARAKAT (IKM)<br>
                            LEMBAGA PELATIHAN KESEHATAN<br>
                            FLASH INSPIRE TRAINING CENTER (FIT-C)<br>
                            KABUPATEN NGANJUK PROVINSI JAWA TIMUR<br>
                            TAHUN {{ $periode }}
                        </td>
                    </tr>
                </table>
                
                <!-- Garis Ganda -->
                <div style="border-top: 3px solid #000; border-bottom: 1px solid #000; height: 2px; margin: 25px 0 40px 0;"></div>

                <!-- Kotak IKM & Responden -->
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <!-- Kiri: Kotak IKM -->
                        <td width="48%" style="vertical-align: top;">
                            <div class="box-ikm">
                                <div class="title">NILAI IKM</div>
                                <div class="nilai-besar">{{ $ikm_page2 }}</div>
                                <div class="mutu">{{ $mutu }}</div>
                            </div>
                        </td>
                        <td width="4%"></td>
                        
                        <!-- Kanan: Kotak Responden -->
                        <td width="48%" style="vertical-align: top;">
                            <div class="box-responden">
                                <div class="title">Nama Layanan : Kepuasan Masyarakat</div>
                                <div class="bold text-center" style="margin-top: 10px; font-size: 13px;">RESPONDEN</div>
                                
                                <table>
                                    <tr>
                                        <td width="25%">Jumlah</td>
                                        <td width="10%"></td>
                                        <td width="65%">{{ $totalResponden }} Orang</td>
                                    </tr>
                                    <tr>
                                        <td>Jenis</td>
                                        <td>L</td>
                                        <td>{{ $demografi['L'] }} Orang</td>
                                    </tr>
                                    <tr>
                                        <td>Kelamin</td>
                                        <td>P</td>
                                        <td>{{ $demografi['P'] }} Orang</td>
                                    </tr>
                                    <tr>
                                        <td>Pendidikan</td>
                                        <td>Diploma</td>
                                        <td>{{ $demografi['Diploma'] }} Orang</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>Sarjana</td>
                                        <td>{{ $demografi['Sarjana'] }} Orang</td>
                                    </tr>
                                </table>
                                
                                <table style="margin-top: auto; margin-bottom: 10px;">
                                    <tr>
                                        <td width="35%">Periode Survey</td>
                                        <td width="65%">{{ $bulan ? date('F', mktime(0,0,0,$bulan,1)) . ' ' : '' }}{{ $periode }}</td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
                </table>

                <!-- Teks Terima Kasih -->
                <div class="text-center" style="margin-top: 50px; font-size: 11px; line-height: 1.4;">
                    TERIMA KASIH ATAS PENILAIAN YANG TELAH ANDA BERIKAN<br>
                    MASUKAN ANDA SANGAT BERMANFAAT UNTUK KEMAJUAN UNIT KAMI AGAR TERUS MEMPERBAIKI<br>
                    DAN MENINGKATKAN KUALITAS PELAYANAN BAGI MASYARAKAT
                </div>

                <!-- Tanda Tangan -->
                <div class="signature">
                    <div class="text-center" style="font-size: 13px;">
                        Direktur<br>FLASH INSPIRE TRAINING CENTER
                    </div>
                    
                    <img src="{{ asset('storage/ikon.png') }}" class="signature-logo" alt="Logo Watermark">
                    
                    <div class="signature-content" style="height: 100px; display: flex; align-items: center; justify-content: center;">
                        @if($ttd_jenis == 'qr')
                            @php $urlValidasi = route('validasi.ikm', ['tahun' => $periode, 'bulan' => $bulan]); @endphp
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=85x85&data={{ urlencode($urlValidasi) }}" alt="QR Validasi" style="background: white; padding: 4px; border-radius: 4px; box-shadow: 0 0 5px rgba(0,0,0,0.2);">
                        @endif
                    </div>
                    
                    <div class="bold" style="font-size: 13px; text-decoration: underline;">
                        dr. WINDY ARI WIJAYA, Sp.An-Ti.
                    </div>
                </div>

            </div>
        </div>
        
    </div>

    <script>
        function downloadPDF() {
            const element = document.getElementById('document-wrapper');
            const opt = {
                margin:       0,
                filename:     'Laporan_IKM_FITC_{{ $periode }}.pdf',
                image:        { type: 'jpeg', quality: 1 },
                html2canvas:  { scale: 2, useCORS: true, scrollY: 0, scrollX: 0 }, 
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };
            html2pdf().set(opt).from(element).save();
        }
    </script>
</body>
</html>