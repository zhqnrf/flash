<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pelunasan Pembayaran - {{ $registrasi->event->nama_event }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .dropzone { border: 2px dashed #cbd5e1; transition: all .2s ease; }
        .dropzone:hover { border-color: #1a365d; background-color: #f0f5fb; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen text-slate-800 pb-14">

    @if(session('success'))
    <script>document.addEventListener('DOMContentLoaded', () => Swal.fire({icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", confirmButtonColor: '#1a365d'}));</script>
    @endif
    @if($errors->any())
    <script>document.addEventListener('DOMContentLoaded', () => Swal.fire({icon: 'error', title: 'Periksa Kembali', html: `{!! implode('<br>', $errors->all()) !!}`, confirmButtonColor: '#1a365d'}));</script>
    @endif

    <div class="max-w-lg mx-auto bg-white shadow-xl mt-0 md:mt-10 rounded-none md:rounded-[2rem] overflow-hidden border border-slate-100">

        <div class="bg-gradient-to-br from-[#1a365d] to-[#0f2942] p-8 text-white">
            <span class="bg-orange-400 text-xs font-bold px-2.5 py-1 rounded-full text-white">Pelunasan Cicilan</span>
            <h1 class="text-xl md:text-2xl font-extrabold mt-3">{{ $registrasi->event->nama_event }}</h1>
            <p class="text-sm text-white/70 mt-1">Atas nama: {{ $registrasi->nama_lengkap }}</p>
        </div>

        <div class="p-6 md:p-8" x-data="{ fileName: '', fileSize: '',
                handleFile(e){ const f=e.target.files[0]; if(!f){this.fileName='';this.fileSize='';return;} this.fileName=f.name; this.fileSize=(f.size/1024).toFixed(0)+' KB'; },
                clearFile(input){ input.value=''; this.fileName=''; this.fileSize=''; }
            }">

            <!-- Ringkasan Tagihan -->
            <div class="bg-orange-50 border border-orange-100 rounded-2xl p-5 mb-6">
                <p class="text-xs font-bold text-orange-700 uppercase tracking-wide mb-1">Sisa Kekurangan Pembayaran</p>
                <p class="text-2xl font-extrabold text-orange-700">Rp {{ number_format($registrasi->kekurangan, 0, ',', '.') }}</p>
                <div class="text-xs text-slate-500 mt-3 space-y-0.5">
                    <p>Total Biaya: <span class="font-bold text-slate-700">Rp {{ number_format($registrasi->event->biaya_pelatihan, 0, ',', '.') }}</span></p>
                    <p>Sudah Dibayar: <span class="font-bold text-slate-700">Rp {{ number_format($registrasi->total_dibayar, 0, ',', '.') }}</span></p>
                    @if($registrasi->event->rekening_pembayaran)
                    <p>Rekening: <span class="font-bold text-slate-700">{{ $registrasi->event->rekening_pembayaran }}</span></p>
                    @endif
                </div>
            </div>

            @if($registrasi->bukti_bayar_terakhir)
            <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4 mb-6 text-sm text-blue-800 font-semibold">
                Anda sudah pernah mengirim bukti pembayaran dan sedang menunggu verifikasi admin. Jika perlu, Anda tetap bisa mengunggah ulang di bawah ini.
            </div>
            @endif

            <form action="{{ route('pembayaran.store', $registrasi->uuid) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2">Upload Bukti Pelunasan <span class="text-red-500">*</span></label>

                    <label for="bukti_bayar_terakhir" class="dropzone flex flex-col items-center justify-center gap-2 rounded-2xl px-6 py-8 cursor-pointer text-center" :class="fileName ? 'border-emerald-400 bg-emerald-50/60' : ''">
                        <input id="bukti_bayar_terakhir" type="file" name="bukti_bayar_terakhir" required accept=".jpg,.jpeg,.png" class="hidden" @change="handleFile($event)">

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
                                <button type="button" @click.prevent.stop="clearFile(document.getElementById('bukti_bayar_terakhir'))" class="ml-auto text-slate-400 hover:text-red-500 transition shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </template>
                    </label>
                </div>

                <button type="submit" class="w-full bg-[#1a365d] hover:bg-[#142c4c] text-white py-4 rounded-xl font-extrabold text-sm shadow-xl transition-all transform hover:-translate-y-0.5">
                    Kirim Bukti Pelunasan
                </button>
                <p class="text-center text-xs text-slate-400">Admin akan memverifikasi dan memperbarui status pembayaran Anda.</p>
            </form>
        </div>
    </div>
</body>
</html>