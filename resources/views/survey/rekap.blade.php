@extends('layouts.admin')
@section('title', 'Rekap Survey Kepuasan (IKM)')

@section('content')

<div class="mb-6">
    <h2 class="text-3xl font-extrabold text-[#1a365d]">Rekap Survey Kepuasan Pelanggan (IKM)</h2>
    <p class="text-gray-500 text-sm mt-1">Perhitungan mengikuti format Indeks Kepuasan Masyarakat (Permenpan RB No. 14/2017).</p>
</div>

<!-- KOTAK LINK FORM SURVEY UNTUK DICOPAS -->
<div class="bg-blue-50 border border-blue-100 rounded-2xl p-5 mb-6 flex flex-col md:flex-row items-center justify-between gap-4">
    <div>
        <h3 class="text-sm font-extrabold text-[#1a365d]">Link Form Survey Publik</h3>
        <p class="text-xs text-blue-600 mt-1">Salin link ini dan bagikan kepada responden/peserta untuk mengisi survey.</p>
    </div>
    <div class="flex items-center gap-2 w-full md:w-auto">
        <input type="text" id="surveyLinkInput" value="{{ route('survey-kepuasan.public') }}" readonly class="w-full md:w-80 px-4 py-2.5 bg-white border border-blue-200 rounded-xl text-sm text-gray-600 focus:outline-none">
        <button onclick="copySurveyLink()" type="button" class="bg-[#1a365d] hover:bg-[#142c4c] text-white px-5 py-2.5 rounded-xl text-sm font-bold transition whitespace-nowrap shadow-sm">
           Link
        </button>
    </div>
</div>

<script>
    function copySurveyLink() {
        var copyText = document.getElementById("surveyLinkInput");
        copyText.select();
        copyText.setSelectionRange(0, 99999); 
        
        navigator.clipboard.writeText(copyText.value).then(() => {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Disalin!',
                    text: 'Link survey berhasil dicopy ke clipboard.',
                    timer: 1500,
                    showConfirmButton: false
                });
            } else {
                alert("Link survey berhasil disalin!");
            }
        });
    }
</script>

<!-- AREA FILTER & CETAK PDF -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    
    <!-- FILTER BULAN & TAHUN -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex flex-col justify-center">
        <h3 class="text-sm font-extrabold text-slate-700 mb-3">Filter Data</h3>
        <form method="GET" action="{{ route('survey-kepuasan.rekap') }}" class="flex flex-col sm:flex-row gap-3 items-end">
            <div class="w-full sm:w-auto flex-1">
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Bulan</label>
                <select name="bulan" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm">
                    <option value="">Semua Bulan</option>
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ request('bulan') == $m ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-auto flex-1">
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Tahun</label>
                <select name="tahun" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm">
                    @foreach(range(date('Y')-2, date('Y')+2) as $y)
                        <option value="{{ $y }}" {{ request('tahun', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2 w-full sm:w-auto">
                <button type="submit" class="bg-[#1a365d] text-white px-5 py-2 rounded-xl text-sm font-bold w-full sm:w-auto">Filter</button>
                <a href="{{ route('survey-kepuasan.rekap') }}" class="bg-gray-100 text-gray-600 hover:bg-gray-200 px-5 py-2 rounded-xl text-sm font-bold text-center w-full sm:w-auto">Reset</a>
            </div>
        </form>
    </div>

    <!-- TOMBOL EXPORT/CETAK -->
    <div class="bg-emerald-50 rounded-2xl border border-emerald-100 p-5 flex flex-col justify-center">
        <div>
            <h3 class="text-sm font-extrabold text-emerald-800">Cetak Laporan IKM</h3>
            <p class="text-xs text-emerald-600 mb-3">Pilih jenis tanda tangan sebelum mencetak.</p>
        </div>
        <form target="_blank" action="{{ route('survey-kepuasan.cetak') }}" method="GET" class="flex flex-col sm:flex-row gap-3 items-end">
            <!-- Parameter Filter disisipkan agar data cetak sesuai dengan filter saat ini -->
            <input type="hidden" name="bulan" value="{{ request('bulan') }}">
            <input type="hidden" name="tahun" value="{{ request('tahun', date('Y')) }}">
            
            <div class="w-full sm:w-auto flex-1">
                <label class="block text-xs font-bold text-emerald-700 mb-1.5">Jenis TTD</label>
                <select name="ttd_jenis" class="w-full px-3 py-2 border border-emerald-200 rounded-xl text-sm text-emerald-800 bg-white cursor-pointer">
                    <option value="manual">TTD Manual (Kosong)</option>
                    <option value="qr">TTD QR Code Validasi</option>
                </select>
            </div>
            <button type="submit" class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2 rounded-xl text-sm font-bold shadow-sm whitespace-nowrap transition-all">
              Generate Laporan
            </button>
        </form>
    </div>
</div>

<!-- RINGKASAN IKM -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Jumlah Responden</p>
        <p class="text-2xl font-extrabold text-slate-800 mt-1">{{ $totalResponden }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Nilai IKM Tertimbang (*)</p>
        <p class="text-2xl font-extrabold text-indigo-600 mt-1">{{ number_format($ikmTertimbang, 3) }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Nilai IKM (**)</p>
        <p class="text-2xl font-extrabold text-emerald-600 mt-1">{{ $ikm }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Mutu Pelayanan</p>
        <p class="text-xl font-extrabold text-amber-600 mt-1">
            {{ explode(' ', $mutu)[0] ?? '-' }} 
            <span class="text-sm font-semibold">{{ isset($mutu) ? str_replace(['(', ')'], '', strstr($mutu, '(')) : '' }}</span>
        </p>
    </div>
</div>

<!-- MATRIKS JAWABAN PER RESPONDEN -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
    <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <h3 class="text-sm font-extrabold text-slate-700 uppercase tracking-wider">Unit Pelayanan Flash Inspire Training Center (FIT-C)</h3>
        @if(request('bulan') || request('tahun'))
            <span class="bg-blue-50 text-blue-600 text-xs font-bold px-3 py-1.5 rounded-lg border border-blue-100">
                Periode: {{ request('bulan') ? date('F', mktime(0,0,0,request('bulan'),1)) : 'Semua Bulan' }} {{ request('tahun', date('Y')) }}
            </span>
        @endif
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-center border-collapse whitespace-nowrap text-sm">
            <thead>
                <tr class="bg-slate-50 text-[#1a365d] text-xs uppercase tracking-wider border-b border-gray-100">
                    <th class="p-3 font-extrabold border-r border-gray-100">No</th>
                    @foreach($unsurs as $u)
                    <th class="p-3 font-extrabold" title="{{ $u->nama_unsur }}">{{ $u->kode }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="text-gray-700">
                @forelse($matriks as $baris)
                <tr class="border-b border-gray-50 hover:bg-slate-50/50 transition">
                    <td class="p-3 font-bold text-slate-400 border-r border-gray-100">{{ $baris['no'] }}</td>
                    @foreach($unsurs as $u)
                    <td class="p-3">{{ $baris[$u->kode] ?? '-' }}</td>
                    @endforeach
                </tr>
                @empty
                <tr><td colspan="{{ $unsurs->count() + 1 }}" class="p-8 text-gray-400 font-bold">Belum ada data responden pada periode ini.</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="bg-slate-50 font-bold text-slate-600 text-xs border-t-2 border-gray-200">
                    <td class="p-3 text-right pr-4 border-r border-gray-200">Σ Nilai/Unsur</td>
                    @foreach($rekapUnsur as $r)
                    <td class="p-3">{{ $unsurs->count() > 0 ? $r['nrr'] * $totalResponden : 0 }}</td>
                    @endforeach
                </tr>
                <tr class="bg-slate-50 font-bold text-[#1a365d] text-xs border-t border-gray-100">
                    <td class="p-3 text-right pr-4 border-r border-gray-200">NRR</td>
                    @foreach($rekapUnsur as $r)
                    <td class="p-3">{{ number_format($r['nrr'], 3) }}</td>
                    @endforeach
                </tr>
                <tr class="bg-indigo-50 font-bold text-indigo-700 text-xs border-t border-indigo-100">
                    <td class="p-3 text-right pr-4 border-r border-indigo-200">NRR Tertimbang</td>
                    @foreach($rekapUnsur as $r)
                    <td class="p-3">{{ number_format($r['nrr_tertimbang'], 3) }}</td>
                    @endforeach
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- KETERANGAN -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 md:p-6 mb-10">
    <h3 class="text-sm font-extrabold text-slate-700 uppercase tracking-wider mb-4">Keterangan</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs text-slate-600">
        <div class="space-y-1.5 p-5 bg-gray-50 rounded-xl border border-gray-100">
            <p><b>U1 s.d. U9</b> = Unsur-Unsur pelayanan</p>
            <p><b>NRR</b> = Nilai rata-rata</p>
            <p><b>IKM</b> = Indeks Kepuasan Masyarakat</p>
            <p><b>*</b> = Jumlah NRR IKM tertimbang</p>
            <p><b>**</b> = Jumlah NRR Tertimbang x 25</p>
            <p><b>NRR Per Unsur</b> = Jumlah nilai per unsur dibagi jumlah kuesioner yang terisi</p>
            <p><b>NRR tertimbang</b> = NRR per unsur x {{ $bobot }} per unsur</p>
        </div>
        <div class="space-y-2.5 px-2">
            @foreach($unsurs as $u)
            <div class="flex items-start gap-3">
                <span class="font-bold text-white bg-[#1a365d] px-2 py-0.5 rounded text-[10px] mt-0.5">{{ $u->kode }}</span>
                <span class="font-medium text-slate-700">{{ $u->nama_unsur }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection