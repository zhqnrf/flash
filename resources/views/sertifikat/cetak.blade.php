<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sertifikat - {{ $registrasi->nama_lengkap }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    @php
        $themeColor = !empty($event->warna_sertifikat)
            ? $event->warna_sertifikat
            : '#0f172a';

        $themeGold = '#d4af37';
    @endphp

    <style>

        /* =========================================================
           ROOT
        ========================================================= */

        :root {
            --theme-primary: {{ $themeColor }};
            --theme-gold: {{ $themeGold }};
            --text-dark: #1e293b;
            --text-gray: #475569;
        }


        /* =========================================================
           PAGE PRINT SETTING
        ========================================================= */

        @page {
            size: A4 landscape;
            margin: 0;
        }


        /* =========================================================
           BODY
        ========================================================= */

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;

            background-color: #e2e8f0;

            margin: 0;
            padding: 20px 0;

            display: flex;
            flex-direction: column;
            align-items: center;

            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }


        /* =========================================================
           ACTION BUTTON
        ========================================================= */

        #action-buttons {
            display: flex;
            gap: 15px;

            margin-bottom: 25px;

            z-index: 100;
        }

        .btn {
            padding: 12px 24px;

            border: none;
            border-radius: 8px;

            cursor: pointer;

            font-weight: 700;
            font-size: 14px;

            box-shadow: 0 4px 6px rgba(0,0,0,0.1);

            transition: 0.2s;
        }

        .btn-print {
            background: #059669;
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);

            box-shadow:
                0 6px 12px rgba(0,0,0,0.15);
        }


        /* =========================================================
           DOCUMENT WRAPPER
        ========================================================= */

        #document-wrapper {
            display: flex;
            flex-direction: column;

            gap: 30px;
        }


        /* =========================================================
           PAGE
        ========================================================= */

        .page {
            width: 297mm;
            height: 210mm;

            min-width: 297mm;
            min-height: 210mm;

            max-width: none;
            max-height: none;

            background: white;

            position: relative;

            box-sizing: border-box;

            box-shadow:
                0 10px 30px rgba(0,0,0,0.15);

            page-break-after: always;
            break-after: page;

            overflow: hidden;
        }


        .page:last-child {
            page-break-after: auto;
            break-after: auto;
        }


        /* =========================================================
           SHAPES & ORNAMENTS & WATERMARK
        ========================================================= */

        .shape-bg {
            position: absolute;

            top: 0;
            left: 0;

            width: 65%;
            height: 100%;

            background: var(--theme-primary);

            opacity: 0.03;

            clip-path:
                polygon(
                    0 0,
                    75% 0,
                    40% 100%,
                    0 100%
                );

            z-index: 1;
        }


        /* =========================================================
           WATERMARK
        ========================================================= */

        .watermark-bg {
            position: absolute;

            top: 50%;
            left: 50%;

            transform:
                translate(-50%, -50%);

            width: 550px;

            opacity: 0.04;

            z-index: 2;

            pointer-events: none;

            filter: grayscale(100%);
        }


        /* =========================================================
           SUDUT KIRI ATAS
        ========================================================= */

        .shape-tl-gold {
            position: absolute;

            top: 0;
            left: 0;

            width: 280px;
            height: 280px;

            background: var(--theme-gold);

            clip-path:
                polygon(
                    0 0,
                    100% 0,
                    0 100%
                );

            z-index: 3;
        }


        .shape-tl-primary {
            position: absolute;

            top: 0;
            left: 0;

            width: 250px;
            height: 250px;

            background: var(--theme-primary);

            clip-path:
                polygon(
                    0 0,
                    100% 0,
                    0 100%
                );

            z-index: 4;
        }


        /* =========================================================
           SUDUT KANAN BAWAH
        ========================================================= */

        .shape-br-gold {
            position: absolute;

            bottom: 0;
            right: 0;

            width: 180px;
            height: 180px;

            background: var(--theme-gold);

            clip-path:
                polygon(
                    100% 0,
                    100% 100%,
                    0 100%
                );

            z-index: 3;
        }


        .shape-br-primary {
            position: absolute;

            bottom: 0;
            right: 0;

            width: 150px;
            height: 150px;

            background: var(--theme-primary);

            clip-path:
                polygon(
                    100% 0,
                    100% 100%,
                    0 100%
                );

            z-index: 4;
        }


        /* =========================================================
           SUDUT KANAN ATAS
        ========================================================= */

        .shape-tr {
            position: absolute;

            top: 0;
            right: 0;

            width: 120px;
            height: 120px;

            background: var(--theme-primary);

            clip-path:
                polygon(
                    100% 0,
                    0 0,
                    100% 100%
                );

            z-index: 3;

            opacity: 0.8;
        }


        /* =========================================================
           SUDUT KIRI BAWAH
        ========================================================= */

        .shape-bl {
            position: absolute;

            bottom: 0;
            left: 0;

            width: 120px;
            height: 120px;

            background: var(--theme-primary);

            clip-path:
                polygon(
                    0 100%,
                    0 0,
                    100% 100%
                );

            z-index: 3;

            opacity: 0.8;
        }


        /* =========================================================
           KONTEN UTAMA
        ========================================================= */

        .page-content {
            position: relative;

            z-index: 10;

            width: 100%;
            height: 100%;

            padding: 30mm 40mm;

            box-sizing: border-box;

            display: flex;

            flex-direction: column;

            justify-content: center;

            text-align: center;
        }


        /* =========================================================
           HEADER
        ========================================================= */

        .header {
            display: flex;

            align-items: center;

            justify-content: center;

            gap: 20px;

            margin-bottom: 25px;
        }


        .header-logo {
            height: 75px;

            width: auto;
        }


        .header-text {
            text-align: left;
        }


        .header-text h2 {
            margin: 0 0 6px 0;

            font-size: 20px;

            font-weight: 800;

            color: var(--theme-primary);

            letter-spacing: 1px;
        }


        .badge-akreditasi {
            background: var(--theme-primary);

            color: white;

            font-size: 10px;

            padding: 5px 12px;

            font-weight: 700;

            border-radius: 4px;

            display: inline-block;
        }


        /* =========================================================
           TITLE
        ========================================================= */

        .title-sertifikat {
            font-family:
                'Playfair Display',
                serif;

            font-size: 52px;

            font-weight: 700;

            color: var(--theme-gold);

            letter-spacing: 12px;

            margin: 0 0 5px 0;
        }


        .nomor-sertifikat {
            font-size: 14px;

            color: var(--text-gray);

            font-weight: 600;

            margin-bottom: 30px;

            letter-spacing: 1px;
        }


        /* =========================================================
           IDENTITAS
        ========================================================= */

        .text-diberikan {
            font-size: 14px;

            font-weight: 700;

            color: var(--text-gray);

            letter-spacing: 2px;

            margin-bottom: 5px;

            text-transform: uppercase;
        }


        .nama-peserta {
            font-family:
                'Playfair Display',
                serif;

            font-size: 40px;

            font-weight: 700;

            color: var(--text-dark);

            margin: 0 0 10px 0;
        }


        .text-sebagai {
            font-size: 16px;

            font-weight: 800;

            color: var(--theme-primary);

            margin: 0 0 20px 0;

            letter-spacing: 2px;

            text-transform: uppercase;
        }


        /* =========================================================
           DESKRIPSI
        ========================================================= */

        .deskripsi {
            font-size: 14px;

            line-height: 1.8;

            color: var(--text-dark);

            width: 85%;

            margin: 0 auto;

            font-weight: 500;
        }


        .deskripsi b {
            color: var(--theme-primary);

            font-weight: 800;
        }


        /* =========================================================
           AREA TTD
        ========================================================= */

        .footer-area {
            display: flex;

            justify-content: flex-end;

            margin-top: 35px;

            padding-right: 20px;
        }


        .ttd-section {
            text-align: center;

            width: 280px;
        }


        .ttd-section p {
            margin: 3px 0;

            font-size: 13px;

            color: var(--text-dark);

            font-weight: 600;
        }


        /* =========================================================
           QR
        ========================================================= */

        .qr-signature {
            margin: 10px auto;

            width: 85px;
            height: 85px;

            padding: 4px;

            border:
                2px solid var(--theme-primary);

            border-radius: 6px;

            background: white;

            box-sizing: border-box;
        }


        .qr-signature img {
            width: 100%;
            height: 100%;

            display: block;
        }


        .nama-ttd {
            font-weight: 800;

            font-size: 15px;

            text-decoration: underline;

            text-underline-offset: 4px;

            color: var(--theme-primary);
        }


        /* =========================================================
           HALAMAN 2
        ========================================================= */

        .h2-title {
            text-align: center;

            margin-bottom: 25px;
        }


        .h2-title h3 {
            font-family:
                'Playfair Display',
                serif;

            font-size: 24px;

            color: var(--theme-primary);

            margin: 0 0 5px 0;
        }


        .h2-title p {
            margin: 0;

            font-size: 14px;

            color: var(--text-gray);

            font-weight: 600;
        }


        /* =========================================================
           TABLE
        ========================================================= */

        .table-container {
            border-radius: 8px;

            overflow: hidden;

            border:
                1px solid #cbd5e1;

            box-shadow:
                0 4px 6px rgba(0,0,0,0.05);
        }


        .tabel-materi {
            width: 100%;

            border-collapse: collapse;

            font-size: 13px;

            background:
                rgba(255,255,255,0.9);
        }


        .tabel-materi th,
        .tabel-materi td {
            padding: 10px 15px;

            border-bottom:
                1px solid #e2e8f0;
        }


        .tabel-materi th {
            background: var(--theme-primary);

            color: white;

            font-weight: 700;

            text-transform: uppercase;

            font-size: 12px;
        }


        .tabel-materi tbody tr:last-child td {
            border-bottom: none;
        }


        .td-center {
            text-align: center;

            font-weight: 600;

            color: var(--text-dark);
        }


        .td-materi {
            text-align: left;

            font-weight: 600;

            color: var(--text-dark);
        }


        .td-unsur {
            font-size: 11px;

            font-weight: 500;

            color: var(--text-gray);

            margin-top: 4px;

            display: block;
        }


        .row-total {
            background: #f8fafc;
        }


        .row-total td {
            font-weight: 800;

            color: var(--theme-primary);

            border-top:
                2px solid var(--theme-primary);
        }


        /* =========================================================
           PRINT MODE
        ========================================================= */

        @media print {

            html {
                width: 297mm !important;
                min-width: 297mm !important;
                max-width: 297mm !important;

                margin: 0 !important;
                padding: 0 !important;
            }


            body {
                width: 297mm !important;

                min-width: 297mm !important;

                max-width: 297mm !important;

                margin: 0 !important;

                padding: 0 !important;

                background: white !important;

                display: block !important;

                overflow: visible !important;

                -webkit-print-color-adjust: exact !important;

                print-color-adjust: exact !important;
            }


            /* Hilangkan tombol saat print */
            #action-buttons {
                display: none !important;
            }


            /* Wrapper */
            #document-wrapper {
                display: block !important;

                width: 297mm !important;

                min-width: 297mm !important;

                max-width: 297mm !important;

                margin: 0 !important;

                padding: 0 !important;

                gap: 0 !important;
            }


            /* Setiap sertifikat = 1 halaman A4 */
            .page {
                width: 297mm !important;

                min-width: 297mm !important;

                max-width: 297mm !important;

                height: 210mm !important;

                min-height: 210mm !important;

                max-height: 210mm !important;

                margin: 0 !important;

                padding: 0 !important;

                box-sizing: border-box !important;

                background: white !important;

                box-shadow: none !important;

                overflow: hidden !important;

                position: relative !important;

                page-break-after: always !important;

                break-after: page !important;

                page-break-inside: avoid !important;

                break-inside: avoid !important;
            }


            /* Halaman terakhir */
            .page:last-child {
                page-break-after: auto !important;

                break-after: auto !important;
            }


            /* Konten */
            .page-content {
                width: 297mm !important;

                height: 210mm !important;

                min-width: 297mm !important;

                min-height: 210mm !important;

                box-sizing: border-box !important;

                position: relative !important;
            }


            /* Jangan pecah elemen penting */
            .header,
            .title-sertifikat,
            .nomor-sertifikat,
            .nama-peserta,
            .deskripsi,
            .footer-area,
            .table-container {
                break-inside: avoid !important;

                page-break-inside: avoid !important;
            }


            /* Pastikan tabel tidak membuat halaman baru */
            .table-container {
                break-inside: avoid !important;

                page-break-inside: avoid !important;
            }


            table {
                page-break-inside: avoid !important;

                break-inside: avoid !important;
            }


            tr {
                page-break-inside: avoid !important;

                break-inside: avoid !important;
            }


            /* Background dan ornament tetap dicetak */
            .shape-bg,
            .shape-tl-gold,
            .shape-tl-primary,
            .shape-br-gold,
            .shape-br-primary,
            .shape-tr,
            .shape-bl,
            .watermark-bg {
                -webkit-print-color-adjust: exact !important;

                print-color-adjust: exact !important;
            }
        }

    </style>
</head>


<body>


    <!-- =========================================================
         TOMBOL PRINT
    ========================================================= -->

    <div id="action-buttons">

        <button
            type="button"
            onclick="window.print()"
            class="btn btn-print"
        >
          Unduh
        </button>

    </div>


    <!-- =========================================================
         DOCUMENT
    ========================================================= -->

    <div id="document-wrapper">


        <!-- =====================================================
             HALAMAN 1
        ===================================================== -->

        <div class="page">


            <!-- Background -->
            <div class="shape-bg"></div>


            <!-- Watermark -->
            <img
                src="{{ asset('storage/ikon.png') }}"
                class="watermark-bg"
                alt="Watermark"
            >


            <!-- Ornament -->
            <div class="shape-tl-gold"></div>

            <div class="shape-tl-primary"></div>

            <div class="shape-br-gold"></div>

            <div class="shape-br-primary"></div>

            <div class="shape-tr"></div>

            <div class="shape-bl"></div>


            <!-- =================================================
                 CONTENT
            ================================================== -->

            <div class="page-content">


                <!-- HEADER -->

                <div class="header">

                    <img
                        src="{{ asset('storage/ikon.png') }}"
                        alt="Logo"
                        class="header-logo"
                    >


                    <div class="header-text">

                        <h2>
                            FLASH INSPIRE TRAINING CENTER
                        </h2>


                        <div class="badge-akreditasi">

                            Lembaga Pelatihan Terakreditasi
                            Madya (B) Kemenkes RI
                            <br>

                            SK. Dirjen SDM Kesehatan
                            No. HK 02.02/F/3885/2025

                        </div>

                    </div>

                </div>


                <!-- TITLE -->

                <div class="title-sertifikat">
                    SERTIFIKAT
                </div>


                <div class="nomor-sertifikat">

                    Nomor:
                    {{ $nomorSertifikat }}

                </div>


                <!-- IDENTITAS -->

                <div class="text-diberikan">

                    Diberikan Kepada

                </div>


                <div class="nama-peserta">

                    {{ $registrasi->nama_lengkap }}

                </div>


                <div class="text-sebagai">

                    Sebagai Peserta

                </div>


                <!-- DESKRIPSI -->

                <div class="deskripsi">

                    Telah mengikuti dan menyelesaikan pelatihan

                    <b>
                        {{ optional($event->pelatihan)->nama_pelatihan ?? $event->nama_event }}
                    </b>

                    <br>


                    Sesuai Kurikulum Kementerian Kesehatan
                    Republik Indonesia yang diselenggarakan oleh

                    <b>
                        Flash Inspire Training Center
                    </b>

                    pada tanggal

                    {{ \Carbon\Carbon::parse($event->tanggal_mulai)->translatedFormat('d') }}

                    -

                    {{ \Carbon\Carbon::parse($event->tanggal_selesai)->translatedFormat('d F Y') }}

                    di

                    {{ $event->lokasi ?? 'Tempat Pelatihan' }}.


                    @if(!empty($event->sertifikat_berlaku_selesai))

                        <br>
                        <br>

                        Masa Berlaku Sertifikat
                        Sampai Dengan

                        <b>
                            {{ \Carbon\Carbon::parse($event->sertifikat_berlaku_selesai)->translatedFormat('d F Y') }}
                        </b>

                    @endif

                </div>


                <!-- =================================================
                     TTD
                ================================================== -->

                <div class="footer-area">


                    <div class="ttd-section">


                        <p>

                            {{ $event->lokasi ?? 'Kediri' }},

                            {{ \Carbon\Carbon::parse($event->tanggal_selesai)->translatedFormat('d F Y') }}

                        </p>


                        <p>

                            Flash Inspire Training Center

                        </p>


                        <!-- QR -->

                        <div class="qr-signature">

                            <img
                                src="https://api.qrserver.com/v1/create-qr-code/?size=90x90&data={{ urlencode($urlValidasi) }}"
                                alt="QR Validasi"
                            >

                        </div>


                        <p class="nama-ttd">

                            dr. Windy Ari Wijaya, Sp.An-Ti.

                        </p>


                        <p
                            style="
                                font-size: 12px;
                                color: var(--text-gray);
                                margin-top: 2px;
                            "
                        >

                            Direktur Utama

                        </p>


                    </div>

                </div>


            </div>

        </div>



        <!-- =====================================================
             HALAMAN 2
        ===================================================== -->

        <div class="page">


            <!-- WATERMARK -->

            <img
                src="{{ asset('storage/ikon.png') }}"
                class="watermark-bg"
                alt="Watermark"
            >


            <!-- ORNAMENT -->

            <div
                class="shape-tl-gold"
                style="
                    width: 150px;
                    height: 150px;
                "
            ></div>


            <div
                class="shape-tl-primary"
                style="
                    width: 130px;
                    height: 130px;
                "
            ></div>


            <div
                class="shape-br-gold"
                style="
                    width: 150px;
                    height: 150px;
                "
            ></div>


            <div
                class="shape-br-primary"
                style="
                    width: 130px;
                    height: 130px;
                "
            ></div>



            <!-- =================================================
                 CONTENT HALAMAN 2
            ================================================== -->

            <div
                class="page-content"
                style="
                    justify-content: flex-start;
                    padding-top: 40mm;
                "
            >


                <!-- TITLE -->

                <div class="h2-title">

                    <h3>

                        Rincian Materi Pelatihan

                    </h3>


                    <p>

                        {{ optional($event->pelatihan)->nama_pelatihan ?? $event->nama_event }}

                    </p>

                </div>



                <!-- =================================================
                     TABLE
                ================================================== -->

                <div class="table-container">


                    <table class="tabel-materi">


                        <thead>

                            <tr>


                                <th style="width: 5%;">

                                    No

                                </th>


                                <th
                                    style="
                                        width: 60%;
                                        text-align: left;
                                    "
                                >

                                    Materi / Mata Pelatihan

                                </th>


                                <th
                                    style="
                                        width: 10%;
                                        text-align: center;
                                    "
                                >

                                    Teori

                                </th>


                                <th
                                    style="
                                        width: 10%;
                                        text-align: center;
                                    "
                                >

                                    Praktik

                                </th>


                                <th
                                    style="
                                        width: 15%;
                                        text-align: center;
                                    "
                                >

                                    Total JP

                                </th>


                            </tr>

                        </thead>


                        <tbody>


                            @forelse($rincianMateri as $i => $m)


                                <tr>


                                    <td class="td-center">

                                        {{ $i + 1 }}

                                    </td>


                                    <td class="td-materi">

                                        {{ $m['nama_materi'] }}


                                        @if(!empty($m['unsur']))

                                            <span class="td-unsur">

                                                Unsur:
                                                {{ $m['unsur'] }}

                                            </span>

                                        @endif

                                    </td>


                                    <td class="td-center">

                                        {{ $m['nilai_teori'] ?: '-' }}

                                    </td>


                                    <td class="td-center">

                                        {{ $m['nilai_praktik'] ?: '-' }}

                                    </td>


                                    <td class="td-center">

                                        {{ $m['jpl'] }} JP

                                    </td>


                                </tr>


                            @empty


                                <tr>


                                    <td
                                        colspan="5"
                                        class="td-center"
                                        style="
                                            padding: 30px;
                                            color: var(--text-gray);
                                        "
                                    >

                                        Belum ada rincian materi
                                        untuk event ini.

                                    </td>


                                </tr>


                            @endforelse



                            <!-- TOTAL -->

                            <tr class="row-total">


                                <td
                                    colspan="2"
                                    style="
                                        text-align: right;
                                        padding-right: 20px;
                                    "
                                >

                                    TOTAL JAM PELAJARAN (JP)

                                </td>


                                <td class="td-center">

                                    {{ $totalTeori }}

                                </td>


                                <td class="td-center">

                                    {{ $totalPraktik }}

                                </td>


                                <td class="td-center">

                                    {{ $totalJpl }} JP

                                </td>


                            </tr>


                        </tbody>

                    </table>

                </div>



                <!-- =================================================
                     TTD HALAMAN 2
                ================================================== -->

                <div
                    class="footer-area"
                    style="
                        margin-top: 40px;
                    "
                >


                    <div class="ttd-section">


                        <p>

                            Flash Inspire Training Center

                        </p>


                        <div
                            class="qr-signature"
                            style="
                                width: 70px;
                                height: 70px;
                            "
                        >

                            <img
                                src="https://api.qrserver.com/v1/create-qr-code/?size=70x70&data={{ urlencode($urlValidasi) }}"
                                alt="QR Validasi"
                            >

                        </div>


                        <p class="nama-ttd">

                            dr. Windy Ari Wijaya, Sp.An-Ti.

                        </p>


                        <p
                            style="
                                font-size: 12px;
                                color: var(--text-gray);
                                margin-top: 2px;
                            "
                        >

                            Direktur Utama

                        </p>


                    </div>

                </div>


            </div>

        </div>


    </div>


</body>
</html>