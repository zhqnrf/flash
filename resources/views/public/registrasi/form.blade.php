<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - {{ $event->nama_event }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* ===== Custom file input (dropzone) ===== */
        .dropzone {
            border: 2px dashed #cbd5e1;
            transition: all .2s ease;
        }
        .dropzone:hover, .dropzone.is-dragover {
            border-color: #1a365d;
            background-color: #f0f5fb;
        }

        /* ===== Custom radio "card" style ===== */
        .radio-card input:checked + div {
            border-color: currentColor;
            box-shadow: 0 0 0 3px rgba(0,0,0,0.04);
        }
        .radio-card input:checked + div .radio-dot {
            transform: scale(1);
        }
        .radio-dot {
            transform: scale(0);
            transition: transform .15s ease;
        }

        /* ===== Custom select arrow ===== */
        select.styled-select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2364748b' viewBox='0 0 16 16'%3E%3Cpath d='M8 11 3 6h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
        }

        /* ===== Scrollbar for detail grid on small screens ===== */
        .detail-scroll::-webkit-scrollbar { height: 6px; }
        .detail-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen text-slate-800 pb-14">

    @if(session('success'))
    <script>document.addEventListener('DOMContentLoaded', () => Swal.fire({icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", confirmButtonColor: '#1a365d'}));</script>
    @endif
    @if(session('error'))
    <script>document.addEventListener('DOMContentLoaded', () => Swal.fire({icon: 'error', title: 'Gagal!', text: "{{ session('error') }}", confirmButtonColor: '#1a365d'}));</script>
    @endif
    @if($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', () => Swal.fire({
            icon: 'error',
            title: 'Periksa Kembali Isian Anda',
            html: `{!! implode('<br>', $errors->all()) !!}`,
            confirmButtonColor: '#1a365d'
        }));
    </script>
    @endif

    <div class="max-w-5xl mx-auto bg-white shadow-xl mt-0 md:mt-10 rounded-none md:rounded-[2rem] overflow-hidden border border-slate-100">

        <!-- ============ BANNER & HEADER ============ -->
        @if($event->banner)
        <div class="w-full h-52 md:h-80 bg-gray-200 overflow-hidden relative">
            <img src="{{ asset('storage/'.$event->banner) }}" class="w-full h-full object-cover object-center" alt="Banner Event">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/85 via-slate-900/20 to-transparent"></div>
            <div class="absolute bottom-5 left-6 right-6 text-white">
                <span class="bg-blue-600 text-xs font-bold px-2.5 py-1 rounded-full text-white shadow">{{ $event->tipe_pelatihan }}</span>
                <span class="bg-white/15 backdrop-blur text-xs font-bold px-2.5 py-1 rounded-full text-white shadow ml-2">{{ $event->jenis_pelatihan }}</span>
                <h1 class="text-2xl md:text-4xl font-extrabold mt-3 leading-tight drop-shadow-md">{{ $event->nama_event }}</h1>
                @if($event->batch)
                <p class="text-sm md:text-base font-semibold text-white/80 mt-1">Batch {{ $event->batch }} &middot; Tahun {{ $event->tahun }}</p>
                @endif
            </div>
        </div>
        @else
        <div class="bg-gradient-to-br from-[#1a365d] to-[#0f2942] p-8 md:p-10 text-white">
            <span class="bg-blue-500 text-xs font-bold px-2.5 py-1 rounded-full text-white">{{ $event->tipe_pelatihan }}</span>
            <span class="bg-white/15 backdrop-blur text-xs font-bold px-2.5 py-1 rounded-full text-white ml-2">{{ $event->jenis_pelatihan }}</span>
            <h1 class="text-2xl md:text-4xl font-extrabold mt-3">{{ $event->nama_event }}</h1>
            @if($event->batch)
            <p class="text-sm md:text-base font-semibold text-white/70 mt-1">Batch {{ $event->batch }} &middot; Tahun {{ $event->tahun }}</p>
            @endif
        </div>
        @endif

        <!-- ============ DETAIL LENGKAP PELATIHAN ============ -->
        @php
            // Kelompokkan semua data pelatihan/event menjadi kategori supaya rapi ditampilkan.
            $detailGroups = [
                'Informasi Pelatihan' => [
                    ['icon' => 'book', 'label' => 'Nama Pelatihan (Master)', 'value' => optional($event->pelatihan)->nama_pelatihan],

                    ['icon' => 'tag', 'label' => 'Tipe Pelatihan', 'value' => $event->tipe_pelatihan],
                    ['icon' => 'briefcase', 'label' => 'Jenis Pelatihan', 'value' => $event->jenis_pelatihan],
                    ['icon' => 'award', 'label' => 'SKP', 'value' => $event->skp ? $event->skp.' SKP' : null],
                ],
                'Waktu Pelaksanaan' => [
                    ['icon' => 'calendar', 'label' => 'Tanggal Mulai', 'value' => \Carbon\Carbon::parse($event->tanggal_mulai)->translatedFormat('d F Y')],
                    ['icon' => 'calendar', 'label' => 'Tanggal Selesai', 'value' => \Carbon\Carbon::parse($event->tanggal_selesai)->translatedFormat('d F Y')],
               ],
                'Sistem & Lokasi' => [
                    ['icon' => 'globe', 'label' => 'Sistem Pelatihan', 'value' => $event->sistem_pelatihan],
                    ['icon' => 'map-pin', 'label' => 'Lokasi', 'value' => $event->lokasi],
                    ['icon' => 'video', 'label' => 'Link Zoom', 'value' => $event->link_zoom, 'link' => $event->link_zoom],
                
                ],
                'Penyelenggara' => [
                    ['icon' => 'building', 'label' => 'Instansi Penyelenggara', 'value' => $event->instansi_penyelenggara],
                ],
                'Sertifikat & Pembayaran' => [
        
                    ['icon' => 'credit-card', 'label' => 'Biaya Pelatihan', 'value' => $event->biaya_pelatihan > 0 ? 'Rp '.number_format($event->biaya_pelatihan,0,',','.') : 'Gratis'],
                    ['icon' => 'bank', 'label' => 'Rekening Pembayaran', 'value' => $event->rekening_pembayaran],
                ],
            ];

            $icons = [
                'book' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
                'hash' => 'M5 9h14M5 15h14M11 4L7 20M17 4l-4 16',
                'tag' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
                'briefcase' => 'M20 7h-3V5a2 2 0 00-2-2H9a2 2 0 00-2 2v2H4a1 1 0 00-1 1v10a2 2 0 002 2h14a2 2 0 002-2V8a1 1 0 00-1-1zM9 5h6v2H9V5z',
                'award' => 'M12 15a5 5 0 100-10 5 5 0 000 10zM8.5 14.5L7 21l5-3 5 3-1.5-6.5',
                'calendar' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                'clock' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                'globe' => 'M3.6 9h16.8M3.6 15h16.8M11.5 3a17 17 0 000 18M12.5 3a17 17 0 010 18M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                'map-pin' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z',
                'video' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z',
                'link' => 'M13.828 10.172a4 4 0 010 5.656l-3 3a4 4 0 01-5.656-5.656l1.5-1.5M10.172 13.828a4 4 0 010-5.656l3-3a4 4 0 015.656 5.656l-1.5 1.5',
                'check-circle' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                'building' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2M5 21H3M9 7h1m0 4h1m4-4h1m-1 4h1M9 21v-4a1 1 0 011-1h4a1 1 0 011 1v4',
                'shield' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622a12.02 12.02 0 00-.382-3.016z',
                'palette' => 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0a4 4 0 004-4V9a2 2 0 012-2h4a2 2 0 012 2v8a4 4 0 01-4 4H7z',
                'credit-card' => 'M3 10h18M7 15h1m4 0h5M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
                'bank' => 'M3 21h18M4 10h16M6 10V6l6-3 6 3v4M4 21v-7h16v7',
            ];
        @endphp

        <div class="p-6 md:p-8 bg-gradient-to-b from-slate-50 to-white border-b border-slate-100">
            <h2 class="text-xs font-extrabold text-slate-400 uppercase tracking-widest mb-4">Detail Lengkap Pelatihan</h2>

            <div class="space-y-6">
                @foreach($detailGroups as $groupTitle => $items)
                    @php $visibleItems = array_filter($items, fn($i) => !empty($i['value'])); @endphp
                    @if(count($visibleItems))
                    <div>
                        <p class="text-[11px] font-bold text-blue-700/70 uppercase tracking-wide mb-2.5">{{ $groupTitle }}</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach($visibleItems as $item)
                            <div class="flex items-start gap-3 bg-white rounded-xl border border-slate-100 p-3.5 shadow-sm">
                                <div class="bg-blue-50 p-2 rounded-lg text-[#1a365d] shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$item['icon']] }}"></path>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">{{ $item['label'] }}</p>
                                    @if(!empty($item['link']))
                                    <a href="{{ $item['link'] }}" target="_blank" class="text-sm font-extrabold text-blue-600 hover:underline break-words">{{ $item['value'] }}</a>
                                    @else
                                    <p class="text-sm font-extrabold text-slate-800 break-words">{{ $item['value'] }}</p>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- ============ FORM REGISTRASI ============ -->
        <div class="p-6 md:p-8" x-data="{
                depan: '{{ old('gelar_depan') }}',
                nama: '{{ old('nama') }}',
                belakang: '{{ old('gelar_belakang') }}',
                komitmen: '',
                fileName: '',
                fileSize: '',
                get fullPreview() {
                    let text = '';
                    if(this.depan) text += this.depan + ' ';
                    text += this.nama;
                    if(this.belakang) text += ', ' + this.belakang;
                    return text.trim() === '' ? 'Belum mengisi nama' : text.trim();
                },
                handleFile(e) {
                    const f = e.target.files[0];
                    if (!f) { this.fileName=''; this.fileSize=''; return; }
                    this.fileName = f.name;
                    this.fileSize = (f.size/1024).toFixed(0) + ' KB';
                },
                clearFile(input) {
                    input.value = '';
                    this.fileName = ''; this.fileSize = '';
                }
            }">

            <form action="{{ route('registrasi.store', $event->uuid) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf

                <!-- PREVIEW NAMA -->
                <div class="bg-blue-50/60 p-4 rounded-2xl border border-blue-100">
                    <h2 class="text-sm font-extrabold text-[#1a365d] mb-1">Preview Penulisan Sertifikat:</h2>
                    <p class="text-lg font-bold text-blue-700 italic" x-text="fullPreview"></p>
                    <p class="text-[10px] text-gray-500 mt-1">*Pastikan penulisan nama dan gelar sudah benar untuk keperluan cetak sertifikat.</p>
                </div>

                <!-- 1. DATA PRIBADI -->
                <div>
                    <h3 class="text-sm font-extrabold text-gray-800 border-b pb-2 mb-4 uppercase tracking-wider">1. Data Pribadi</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-5">
                        <div class="md:col-span-1">
                            <label class="block text-xs font-bold text-slate-600 mb-1.5">Gelar Depan</label>
                            <input type="text" name="gelar_depan" x-model="depan" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm font-medium transition" placeholder="Dr. / Ns.">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-600 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="nama" x-model="nama" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm font-medium transition" placeholder="Contoh: Budi Santoso">
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-xs font-bold text-slate-600 mb-1.5">Gelar Belakang</label>
                            <input type="text" name="gelar_belakang" x-model="belakang" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm font-medium transition" placeholder="S.Kep., Ners">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1.5">Tempat Lahir <span class="text-red-500">*</span></label>
                            <input type="text" name="tempat_lahir" required value="{{ old('tempat_lahir') }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm font-medium transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1.5">Tanggal Lahir <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_lahir" required value="{{ old('tanggal_lahir') }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm font-medium transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1.5">NIK (16 Digit) <span class="text-red-500">*</span></label>
                            <input type="text" name="nik" required minlength="16" maxlength="16" inputmode="numeric" value="{{ old('nik') }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm font-medium transition" placeholder="16 Digit NIK">
                        </div>
                        <div>
    <label class="block text-xs font-bold text-slate-600 mb-1.5">Nomor WhatsApp Aktif <span class="text-red-500">*</span></label>
    <div class="flex">
        <span class="inline-flex items-center px-4 bg-slate-100 border border-r-0 border-slate-200 rounded-l-xl text-sm font-bold text-slate-500">+62</span>
        <input type="tel" name="no_whatsapp" required inputmode="numeric"
               value="{{ old('no_whatsapp') }}"
               class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-r-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm font-medium transition"
               placeholder="81234567890">
    </div>
    <p class="text-[10px] text-gray-400 mt-1">Masukkan tanpa angka 0 di depan, contoh: 81234567890</p>
</div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1.5">Email Plataran Sehat <span class="text-red-500">*</span></label>
                            <input type="email" name="email_plataran_sehat" required value="{{ old('email_plataran_sehat') }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm font-medium transition" placeholder="contoh@gmail.com">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Alamat Lengkap <span class="text-red-500">*</span></label>
                        <textarea name="alamat_lengkap" rows="2" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm font-medium transition" placeholder="Masukkan alamat domisili...">{{ old('alamat_lengkap') }}</textarea>
                    </div>
                </div>

                <!-- 2. DATA PEKERJAAN / INSTANSI -->
                <div>
                    <h3 class="text-sm font-extrabold text-gray-800 border-b pb-2 mb-4 uppercase tracking-wider">2. Data Pekerjaan & Instansi</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1.5">Instansi Bekerja <span class="text-red-500">*</span></label>
                            <!-- FIX: name harus "instansi" agar cocok dengan validasi controller -->
                            <input type="text" name="instansi" required value="{{ old('instansi') }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm font-medium transition" placeholder="Contoh: RSUD Simpang Lima Gumul">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1.5">Departemen / Ruangan</label>
                            <input type="text" name="departemen" value="{{ old('departemen') }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm font-medium transition" placeholder="Contoh: IGD / ICU">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1.5">NIP (Opsional, khusus ASN)</label>
                            <input type="text" name="nip" value="{{ old('nip') }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm font-medium transition" placeholder="Nomor Induk Pegawai">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1.5">Pangkat & Golongan (Opsional, khusus ASN)</label>
                            <input type="text" name="pangkat_golongan" value="{{ old('pangkat_golongan') }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm font-medium transition" placeholder="Contoh: Penata Muda Tk. I / IIIb">
                        </div>
                    </div>
                </div>

                <!-- 3. KELENGKAPAN TAMBAHAN -->
                <div>
                    <h3 class="text-sm font-extrabold text-gray-800 border-b pb-2 mb-4 uppercase tracking-wider">3. Kelengkapan Tambahan</h3>

                    @if(in_array($event->sistem_pelatihan, ['Luring', 'Blended']))
                    <div class="mb-5 p-4 bg-amber-50 rounded-xl border border-amber-100">
                        <label class="block text-xs font-extrabold text-amber-800 mb-2">Ukuran Kaos Pelatihan <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <select name="ukuran_kaos" required class="styled-select w-full px-4 py-3 bg-white border border-amber-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:outline-none text-sm font-medium transition cursor-pointer">
                                <option value="">-- Pilih Ukuran Kaos --</option>
                                <option value="M">M Ukuran (Lebar 47 cm | Panjang 67 cm)</option>
                                <option value="L">L Ukuran (Lebar 49 cm | Panjang 69 cm)</option>
                                <option value="XL">XL Ukuran (Lebar 51 cm | Panjang 71 cm)</option>
                                <option value="XXL">XXL Ukuran (Lebar 53 cm | Panjang 71 cm)</option>
                                <option value="XXXL">XXXL Ukuran (Lebar 58 cm | Panjang 77 cm)</option>
                            </select>
                        </div>
                    </div>
                    @endif

                    @if($event->biaya_pelatihan > 0)
                    <div class="mb-5">
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Upload Bukti Bayar Pertama <span class="text-red-500">*</span></label>
                        <p class="text-xs text-gray-500 mb-3">Biaya Pelatihan: <span class="font-bold text-blue-600">Rp {{ number_format($event->biaya_pelatihan, 0, ',', '.') }}</span> | Rekening: <span class="font-bold">{{ $event->rekening_pembayaran }}</span></p>

                        <!-- CUSTOM STYLED FILE INPUT (dropzone) -->
                        <label
                            for="bukti_bayar_pertama"
                            class="dropzone flex flex-col items-center justify-center gap-2 rounded-2xl px-6 py-8 cursor-pointer text-center"
                            :class="fileName ? 'border-emerald-400 bg-emerald-50/60' : ''"
                        >
                            <input id="bukti_bayar_pertama" type="file" name="bukti_bayar_pertama" required
                                   accept=".jpg,.jpeg,.png"
                                   class="hidden"
                                   @change="handleFile($event)">

                            <template x-if="!fileName">
                                <div class="flex flex-col items-center gap-2">
                                    <div class="bg-blue-100 p-3 rounded-full text-blue-700">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M12 12v9m0-9l-3 3m3-3l3 3"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-bold text-slate-700">Klik untuk pilih file</p>
                                    <p class="text-xs text-slate-400">JPG atau PNG, maks. 2MB</p>
                                </div>
                            </template>

                            <template x-if="fileName">
                                <div class="flex items-center gap-3 w-full max-w-sm">
                                    <div class="bg-emerald-100 p-3 rounded-full text-emerald-700 shrink-0">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div class="text-left min-w-0">
                                        <p class="text-sm font-bold text-slate-700 truncate" x-text="fileName"></p>
                                        <p class="text-xs text-slate-400" x-text="fileSize"></p>
                                    </div>
                                    <button type="button"
                                            @click.prevent.stop="clearFile(document.getElementById('bukti_bayar_pertama'))"
                                            class="ml-auto text-slate-400 hover:text-red-500 transition shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </label>
                    </div>
                    @endif
                </div>

                <!-- 4. KOMITMEN PESERTA -->
                <div class="bg-slate-100 p-5 rounded-2xl border border-slate-200">
                    <h3 class="text-sm font-extrabold text-gray-800 mb-3 uppercase tracking-wider">Komitmen Peserta Pelatihan</h3>
                    <p class="text-sm text-gray-600 mb-2 font-semibold">Dengan ini saya menyatakan bahwa :</p>
                    <ul class="list-decimal list-inside text-sm text-gray-600 space-y-1 mb-5">
                        <li>Bersedia mengikuti seluruh rangkaian kegiatan pelatihan sesuai jadwal yang ditentukan baik secara daring atau luring.</li>
                        <li>Komitmen mengikuti pelatihan dengan sungguh sungguh dan mematuhi peraturan serta tata tertib yang berlaku.</li>
                        <li>Memberikan informasi yang benar dan jujur dalam formulir ini.</li>
                        <li>Saya menyadari bahwa pelatihan ini bertujuan untuk meningkatkan kompetensi saya sebagai tenaga kesehatan.</li>
                    </ul>

                    <!-- CUSTOM STYLED RADIO "CARDS" -->
                    <!-- FIX: value harus "Ya" (bukan "ya") agar cocok dengan validasi controller: required|in:Ya -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <label class="radio-card cursor-pointer">
                            <input type="radio" name="komitmen" value="Ya" x-model="komitmen" required class="sr-only">
                            <div class="flex items-center gap-3 p-4 rounded-xl border-2 border-slate-200 bg-white text-emerald-700 transition">
                                <span class="radio-dot w-3 h-3 rounded-full bg-emerald-600 shrink-0 ring-2 ring-emerald-600 ring-offset-2"></span>
                                <span class="text-sm font-bold">Ya, saya menyetujui komitmen ini</span>
                            </div>
                        </label>
                        <label class="radio-card cursor-pointer">
                            <input type="radio" name="komitmen" value="Tidak" x-model="komitmen" class="sr-only">
                            <div class="flex items-center gap-3 p-4 rounded-xl border-2 border-slate-200 bg-white text-red-600 transition">
                                <span class="radio-dot w-3 h-3 rounded-full bg-red-600 shrink-0 ring-2 ring-red-600 ring-offset-2"></span>
                                <span class="text-sm font-bold">Tidak, saya tidak menyetujui komitmen ini</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100">
                    <button type="submit" :disabled="komitmen !== 'Ya'"
                            :class="komitmen === 'Ya' ? 'bg-[#1a365d] hover:bg-[#142c4c]' : 'bg-gray-400 cursor-not-allowed'"
                            class="w-full text-white py-4 rounded-xl font-extrabold text-sm shadow-xl transition-all transform hover:-translate-y-0.5">
                        Kirim Pendaftaran
                    </button>
                    <p class="text-center text-xs text-slate-400 mt-4">Setelah mendaftar, Admin akan memverifikasi data dan status pembayaran Anda.</p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>