@extends('layouts.admin')
@section('title', 'Tambah Event')

@section('content')
<!-- Plugin CSS & JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('error'))
<script>document.addEventListener('DOMContentLoaded', () => Swal.fire('Gagal', "{{ session('error') }}", 'error'));</script>
@endif

<div class="max-w-full bg-white p-8 md:p-10 rounded-2xl shadow-sm border border-gray-100">
    <div class="flex justify-between items-center mb-8 pb-4 border-b">
        <div>
            <h2 class="text-3xl font-extrabold text-[#1a365d]">Create New Event</h2>
            <p class="text-gray-500 text-sm mt-1">Konfigurasi pengaturan event, pemetaan fasilitator, dan generate link otomatis.</p>
        </div>
        <a href="{{ route('event.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-5 py-2.5 rounded-xl font-bold text-sm transition-all">← Batal</a>
    </div>

    <form action="{{ route('event.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf

        <!-- 1. INFORMASI DASAR -->
        <div>
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">1. Informasi Dasar</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-2">
                    <label class="block font-bold text-sm text-gray-700 mb-2">Nama Event <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_event" value="{{ old('nama_event') }}" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#1ba1e2] font-medium text-sm" placeholder="Contoh: Pelatihan BHD Gelombang 1">
                </div>
                <div>
                    <label class="block font-bold text-sm text-gray-700 mb-2">Tahun <span class="text-red-500">*</span></label>
                    <input type="number" name="tahun" value="{{ old('tahun', date('Y')) }}" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#1ba1e2] font-medium text-sm">
                </div>
                <div>
                    <label class="block font-bold text-sm text-gray-700 mb-2">Batch / Angkatan</label>
                    <input type="text" name="batch" value="{{ old('batch') }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#1ba1e2] font-medium text-sm" placeholder="Contoh: Batch V">
                </div>
                <div>
                    <label class="block font-bold text-sm text-gray-700 mb-2">Tipe Pelatihan <span class="text-red-500">*</span></label>
                    <select name="tipe_pelatihan" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#1ba1e2] font-medium text-sm">
                        <option value="Pelatihan">Pelatihan</option>
                        <option value="Workshop">Workshop</option>
                        <option value="Webinar">Webinar</option>
                        <option value="Seminar">Seminar</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-sm text-gray-700 mb-2">Jenis Pelatihan <span class="text-red-500">*</span></label>
                    <select name="jenis_pelatihan" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#1ba1e2] font-medium text-sm">
                        <option value="Mandiri">Mandiri</option>
                        <option value="Kerjasama">Kerjasama</option>
                    </select>
                </div>
                <div class="md:col-span-3">
                    <label class="block font-bold text-sm text-gray-700 mb-2">Instansi Penyelenggara</label>
                    <input type="text" name="instansi_penyelenggara" value="{{ old('instansi_penyelenggara', 'Flash Inspire Emergency') }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#1ba1e2] font-medium text-sm">
                    <p class="text-xs text-gray-400 mt-1">Bisa diketik manual lebih dari satu institusi (Contoh: Flash Inspire, RSUD X)</p>
                </div>
            </div>
        </div>

        <!-- 2. WAKTU, LOKASI & SISTEM -->
        <div>
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">2. Pelaksanaan & Sistem</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block font-bold text-sm text-gray-700 mb-2">Tanggal Mulai <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#1ba1e2] font-medium text-sm">
                </div>
                <div>
                    <label class="block font-bold text-sm text-gray-700 mb-2">Tanggal Selesai <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#1ba1e2] font-medium text-sm">
                </div>
                <div>
                    <label class="block font-bold text-sm text-gray-700 mb-2">Waktu Buka Presensi <span class="text-red-500">*</span></label>
                    <input type="time" name="waktu_presensi_mulai" value="{{ old('waktu_presensi_mulai') }}" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#1ba1e2] font-medium text-sm">
                </div>
                <div>
                    <label class="block font-bold text-sm text-gray-700 mb-2">Waktu Tutup Presensi <span class="text-red-500">*</span></label>
                    <input type="time" name="waktu_presensi_selesai" value="{{ old('waktu_presensi_selesai') }}" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#1ba1e2] font-medium text-sm">
                </div>
                <div>
                    <label class="block font-bold text-sm text-gray-700 mb-2">Sistem Pelatihan <span class="text-red-500">*</span></label>
                    <select name="sistem_pelatihan" id="sistem_pelatihan" onchange="toggleZoom()" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#1ba1e2] font-medium text-sm">
                        <option value="Luring">Luring (Tatap Muka)</option>
                        <option value="Daring">Daring (Online)</option>
                        <option value="Blended">Blended (Campuran)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-sm text-gray-700 mb-2">Lokasi Fisik (Untuk Luring/Blended)</label>
                    <input type="text" name="lokasi" value="{{ old('lokasi') }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#1ba1e2] font-medium text-sm" placeholder="Nama Gedung / Hotel / Ruangan">
                </div>
                <div id="zoom_container" class="md:col-span-2 hidden p-4 bg-blue-50 border border-blue-100 rounded-xl">
                    <label class="block font-bold text-sm text-blue-800 mb-2">Link Zoom Meeting (Untuk Daring / Blended)</label>
                    <input type="url" name="link_zoom" id="link_zoom" value="{{ old('link_zoom') }}" class="w-full px-4 py-3 bg-white border border-blue-200 rounded-xl focus:outline-none focus:border-blue-500" placeholder="https://zoom.us/j/123456789...">
                </div>
            </div>
        </div>

        <!-- 3. MATERI & FASILITATOR (AJAX MATCHING) -->
        <div>
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">3. Penugasan Fasilitator & Materi</h3>
            <div class="mb-4">
                <label class="block font-bold text-sm text-gray-700 mb-2">Pilih Master Pelatihan <span class="text-red-500">*</span></label>
                <select name="pelatihan_id" id="choices-pelatihan" onchange="loadMateriFasilitator(this.value)" required class="w-full">
                    <option value="">-- Pilih Pelatihan (Otomatis meload materi) --</option>
                    @foreach($pelatihans as $p)
                        <option value="{{ $p->id }}">{{ $p->nama_pelatihan }}</option>
                    @endforeach
                </select>
            </div>
            <div id="fasilitator-mapping-container" class="bg-slate-50 border border-slate-200 rounded-xl p-5 min-h-[100px]">
                <p class="text-gray-400 text-sm text-center italic mt-6">Silakan pilih Master Pelatihan di atas untuk melihat daftar materi dan memetakan fasilitator.</p>
            </div>
        </div>

        <!-- 4. KELENGKAPAN & SERTIFIKAT -->
        <div>
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">4. Kelengkapan, Sertifikat & Biaya</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Upload Banner (Styled) -->
                <div>
                    <label class="block font-bold text-sm text-gray-700 mb-2">Banner Event (JPG/PNG, Max 2MB)</label>
                    <div class="relative border-2 border-dashed border-gray-200 rounded-2xl p-4 bg-gray-50 hover:bg-gray-100/50 transition-all text-center cursor-pointer">
                        <input type="file" name="banner" accept="image/png, image/jpeg" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full" onchange="validateAndPreviewBanner(this)">
                        <div class="flex flex-col items-center justify-center space-y-1">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <p id="banner-label" class="text-sm font-bold text-gray-600">Klik untuk unggah banner</p>
                            <p class="text-[10px] text-gray-400">JPG, PNG (Maks. 2MB)</p>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block font-bold text-sm text-gray-700 mb-2">Link Drive Materi Peserta</label>
                    <input type="url" name="link_materi" value="{{ old('link_materi') }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#1ba1e2] font-medium text-sm" placeholder="https://drive.google.com/...">
                </div>
                <div>
                    <label class="block font-bold text-sm text-gray-700 mb-2">Jumlah SKP</label>
                    <input type="number" name="skp" value="{{ old('skp', '0') }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#1ba1e2] font-medium text-sm">
                </div>
                <div>
                    <label class="block font-bold text-sm text-gray-700 mb-2">Fitur Presensi Peserta <span class="text-red-500">*</span></label>
                    <select name="has_presensi" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#1ba1e2] font-medium text-sm">
                        <option value="1">Aktifkan Presensi</option>
                        <option value="0">Tidak Ada Presensi</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-sm text-gray-700 mb-2">Warna Sertifikat / Tema</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="warna_sertifikat" value="{{ old('warna_sertifikat', '#1a365d') }}" class="h-12 w-16 bg-white border border-gray-200 rounded-xl cursor-pointer">
                        <span class="text-xs text-gray-400">Pilih warna dominan sertifikat</span>
                    </div>
                </div>
                <div>
                    <label class="block font-bold text-sm text-gray-700 mb-2">Format Nomor Sertifikat</label>
                    <input type="text" name="nomor_sertifikat" value="{{ old('nomor_sertifikat') }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#1ba1e2] font-medium text-sm" placeholder="Contoh: 123/SK/2026">
                </div>
                <!-- Masa Berlaku Sertifikat -->
<div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-gray-50 border border-gray-200 rounded-xl">
    <div>
        <label class="block font-bold text-sm text-gray-700 mb-2">Sertifikat Berlaku Mulai</label>
        <input type="date" name="sertifikat_berlaku_mulai" value="{{ old('sertifikat_berlaku_mulai') }}" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#1ba1e2] font-medium text-sm">
    </div>
    <div>
        <label class="block font-bold text-sm text-gray-700 mb-2">Sertifikat Berlaku Selesai</label>
        <input type="date" name="sertifikat_berlaku_selesai" value="{{ old('sertifikat_berlaku_selesai') }}" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-[#1ba1e2] font-medium text-sm">
    </div>
</div>
                <!-- Biaya Pelatihan (Format Rupiah Otomatis) -->
                <div>
                    <label class="block font-bold text-sm text-gray-700 mb-2">Biaya Pelatihan</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 font-bold text-gray-500">Rp</span>
                        <input type="text" id="biaya_formatted" value="{{ old('biaya_pelatihan', '0') }}" class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#1ba1e2] font-medium text-sm" onkeyup="formatCurrency(this)">
                        <input type="hidden" name="biaya_pelatihan" id="biaya_actual" value="{{ old('biaya_pelatihan', '0') }}">
                    </div>
                </div>
                <div>
                    <label class="block font-bold text-sm text-gray-700 mb-2">Rekening Pembayaran / Info Bayar</label>
                    <input type="text" name="rekening_pembayaran" value="{{ old('rekening_pembayaran') }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#1ba1e2] font-medium text-sm" placeholder="Contoh: BCA 12345678 a.n Panitia">
                </div>
            </div>
        </div>

        <div class="pt-6 border-t border-gray-100 flex justify-end">
            <button type="submit" class="bg-[#1a365d] hover:bg-[#1ba1e2] text-white px-8 py-4 rounded-xl font-extrabold text-sm shadow-xl hover:-translate-y-1 transition-all">
                Simpan & Generate Link Event
            </button>
        </div>
    </form>
</div>

<!-- Scripts (Choices, Logic Zoom, File Validation, Rupiah Formatting, AJAX) -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectPelatihan = document.getElementById('choices-pelatihan');
        if(selectPelatihan) {
            new Choices(selectPelatihan, { removeItemButton: true, searchEnabled: true });
        }
        const biayaInput = document.getElementById('biaya_formatted');
        if(biayaInput) {
            formatCurrency(biayaInput);
        }
    });

    function toggleZoom() {
        const val = document.getElementById('sistem_pelatihan').value;
        const container = document.getElementById('zoom_container');
        if (val === 'Daring' || val === 'Blended') container.classList.remove('hidden');
        else { container.classList.add('hidden'); document.getElementById('link_zoom').value = ''; }
    }

    function validateAndPreviewBanner(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const maxSize = 2 * 1024 * 1024; // 2 MB
            if (file.size > maxSize) {
                Swal.fire({
                    icon: 'warning', title: 'File Terlalu Besar!',
                    text: `Ukuran file ${(file.size / (1024*1024)).toFixed(2)} MB. Maksimal 2 MB!`,
                    confirmButtonColor: '#1a365d', customClass: { popup: 'rounded-2xl' }
                });
                input.value = ''; 
                document.getElementById('banner-label').innerText = 'Klik untuk unggah banner';
            } else {
                document.getElementById('banner-label').innerText = file.name;
            }
        }
    }

    function formatCurrency(input) {
        let rawValue = input.value.replace(/[^0-9]/g, '');
        if(rawValue === '') rawValue = '0';
        document.getElementById('biaya_actual').value = parseInt(rawValue, 10);
        input.value = new Intl.NumberFormat('id-ID').format(rawValue);
    }

    function loadMateriFasilitator(pelatihan_id) {
        const container = document.getElementById('fasilitator-mapping-container');
        if (!pelatihan_id) {
            container.innerHTML = '<p class="text-gray-400 text-sm text-center italic mt-6">Silakan pilih Master Pelatihan di atas untuk melihat daftar materi dan memetakan fasilitator.</p>';
            return;
        }
        container.innerHTML = '<div class="flex justify-center p-5"><span class="text-blue-500 font-bold animate-pulse">Memuat materi & fasilitator...</span></div>';

        fetch(`/event/ajax/materi-fasilitator/${pelatihan_id}`)
            .then(res => res.json())
            .then(data => {
                if(data.length === 0) {
                    container.innerHTML = `<div class="bg-red-50 text-red-600 p-4 rounded-xl font-bold border border-red-200">
                        ⚠️ Pelatihan ini belum memiliki materi. Harap tambahkan materi di Master Evaluasi Materi terlebih dahulu.
                    </div>`;
                    return;
                }
                let html = '<div class="space-y-4">';
                data.forEach((materi, index) => {
                    html += `<div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2 py-1 rounded">Materi ${index + 1}</span>
                            <h4 class="font-extrabold text-gray-800 mt-2">${materi.nama_materi}</h4>
                        </div>
                        <div class="md:w-1/2">`;

                    if (materi.fasilitators.length === 0) {
                        html += `<div class="text-xs text-red-500 font-bold bg-red-50 p-2 rounded-lg border border-red-100">
                            Peringatan: Belum ada fasilitator yang mengampu materi ini di Master Fasilitator! Penilaian materi ini tidak dapat dilakukan.
                        </div>
                        <input type="hidden" name="fasilitator_materi[${materi.materi_id}]" value="">`;
                    } else {
                        html += `<label class="text-xs font-bold text-gray-500 mb-1 block">Pilih Fasilitator Pengajar:</label>
                        <select name="fasilitator_materi[${materi.materi_id}]" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm font-bold text-gray-700">`;
                        materi.fasilitators.forEach(fas => { html += `<option value="${fas.id}">${fas.nama_fasilitator}</option>`; });
                        html += `</select>
                        <label class="text-xs font-bold text-gray-500 mb-1 mt-2 block">Tanggal Materi Ini Diajarkan:</label>
                        <input type="date" name="tanggal_sesi[${materi.materi_id}]" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm font-bold text-gray-700">
                        <p class="text-[10px] text-gray-400 mt-1">Evaluasi materi ini baru muncul ke peserta pada/setelah tanggal ini.</p>`;
                    }
                    html += `</div></div>`;
                });
                html += '</div>';
                container.innerHTML = html;
            })
            .catch(error => {
                container.innerHTML = `<div class="text-red-500 font-bold p-4">Terjadi kesalahan koneksi saat memuat data.</div>`;
            });
    }
</script>
@endsection