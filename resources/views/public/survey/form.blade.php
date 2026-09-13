<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Survey Kepuasan Pelanggan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        input[type=radio] { position: absolute; opacity: 0; width: 0; height: 0; }
        select.styled-select {
            -webkit-appearance: none; appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2364748b' viewBox='0 0 16 16'%3E%3Cpath d='M8 11 3 6h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 1rem center;
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen text-slate-800 pb-14">

    @if(session('success'))
    <script>document.addEventListener('DOMContentLoaded', () => Swal.fire({icon: 'success', title: 'Terima Kasih!', text: "{{ session('success') }}", confirmButtonColor: '#1a365d'}));</script>
    @endif
    @if($errors->any())
    <script>document.addEventListener('DOMContentLoaded', () => Swal.fire({icon: 'error', title: 'Periksa Kembali', html: `{!! implode('<br>', $errors->all()) !!}`, confirmButtonColor: '#1a365d'}));</script>
    @endif

    <div class="max-w-2xl mx-auto bg-white shadow-xl mt-0 md:mt-10 rounded-none md:rounded-[2rem] overflow-hidden border border-slate-100">

        <div class="bg-gradient-to-br from-[#1a365d] to-[#0f2942] p-8 text-white">
            <span class="bg-blue-400 text-xs font-bold px-2.5 py-1 rounded-full text-white">Survey Kepuasan Pelanggan</span>
            <h1 class="text-xl md:text-2xl font-extrabold mt-3">Survey Kepuasan Pelanggan Terhadap Lembaga Penyelenggara Pelatihan</h1>
            <p class="text-sm text-white/70 mt-1">Masukan Anda sangat berarti untuk peningkatan kualitas layanan kami.</p>
        </div>

        <div class="p-6 md:p-8" x-data="{ pendidikan: '{{ old('pendidikan_terakhir') }}' }">
            <form action="{{ route('survey-kepuasan.store') }}" method="POST" class="space-y-8">
                @csrf

                <!-- ===== DATA RESPONDEN ===== -->
                <div>
                    <h2 class="text-xs font-extrabold text-slate-400 uppercase tracking-widest mb-4">Data Responden</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1.5">Tanggal Survey <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_survey" required value="{{ old('tanggal_survey', date('Y-m-d')) }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm font-medium transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1.5">Usia <span class="text-red-500">*</span></label>
                            <input type="number" name="usia" required min="10" max="100" value="{{ old('usia') }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm font-medium transition" placeholder="Contoh: 30">
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="block text-xs font-bold text-slate-600 mb-2">Jenis Kelamin <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" name="jenis_kelamin" value="Laki-laki" class="sr-only peer" {{ old('jenis_kelamin') == 'Laki-laki' ? 'checked' : '' }}>
                                <div class="text-center p-3 rounded-xl border-2 border-slate-200 font-bold text-slate-500 bg-slate-50 peer-checked:bg-[#1a365d] peer-checked:border-[#1a365d] peer-checked:text-white transition text-sm">Laki-laki</div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="jenis_kelamin" value="Perempuan" class="sr-only peer" {{ old('jenis_kelamin') == 'Perempuan' ? 'checked' : '' }}>
                                <div class="text-center p-3 rounded-xl border-2 border-slate-200 font-bold text-slate-500 bg-slate-50 peer-checked:bg-[#1a365d] peer-checked:border-[#1a365d] peer-checked:text-white transition text-sm">Perempuan</div>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1.5">Pendidikan Terakhir <span class="text-red-500">*</span></label>
                            <select name="pendidikan_terakhir" x-model="pendidikan" required class="styled-select w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm font-medium transition cursor-pointer">
                                <option value="">-- Pilih --</option>
                                <option value="SD">SD</option>
                                <option value="SMP">SMP</option>
                                <option value="SMA">SMA</option>
                                <option value="Diploma (D1, D2, D3)">Diploma (D1, D2, D3)</option>
                                <option value="Sarjana (D4, S1)">Sarjana (D4, S1)</option>
                                <option value="Yang lain">Yang lain</option>
                            </select>
                        </div>
                        <div x-show="pendidikan === 'Yang lain'">
                            <label class="block text-xs font-bold text-slate-600 mb-1.5">Sebutkan Pendidikan Lainnya</label>
                            <input type="text" name="pendidikan_lainnya" value="{{ old('pendidikan_lainnya') }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm font-medium transition">
                        </div>
                        <div :class="pendidikan === 'Yang lain' ? '' : 'md:col-span-2'">
                            <label class="block text-xs font-bold text-slate-600 mb-1.5">Pekerjaan <span class="text-red-500">*</span></label>
                            <input type="text" name="pekerjaan" required value="{{ old('pekerjaan') }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm font-medium transition" placeholder="Contoh: Perawat, ASN, Wiraswasta">
                        </div>
                    </div>
                </div>

                <!-- ===== PENDAPAT RESPONDEN ===== -->
                <div>
                    <h2 class="text-xs font-extrabold text-slate-400 uppercase tracking-widest mb-1">Pendapat Responden Terkait Pelayanan</h2>
                    <p class="text-[11px] text-slate-400 mb-4">Pilih sesuai dengan jawaban Anda</p>

                    <div class="space-y-5">
                        @foreach($unsurs as $u)
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <p class="text-sm font-bold text-slate-700 mb-3">{{ $loop->iteration }}. {{ $u->pertanyaan }}</p>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach($u->opsi_jawaban as $idx => $opsi)
                                <label class="cursor-pointer">
                                    <input type="radio" name="nilai[{{ $u->id }}]" value="{{ $idx + 1 }}" class="sr-only peer" {{ old('nilai.'.$u->id) == ($idx + 1) ? 'checked' : '' }}>
                                    <div class="text-center px-3 py-2.5 rounded-lg border-2 border-slate-200 font-semibold text-slate-500 bg-white peer-checked:bg-[#1a365d] peer-checked:border-[#1a365d] peer-checked:text-white transition text-xs">{{ $opsi }}</div>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <button type="submit" class="w-full bg-[#1a365d] hover:bg-[#142c4c] text-white py-4 rounded-xl font-extrabold text-sm shadow-xl transition-all transform hover:-translate-y-0.5">
                    Kirim Survey
                </button>
            </form>
        </div>
    </div>
</body>
</html>