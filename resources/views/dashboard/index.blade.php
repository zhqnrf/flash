@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div x-data="{}" class="space-y-6 pb-10">

    {{-- ============================================ --}}
    {{-- HEADER + FILTER RENTANG WAKTU --}}
    {{-- ============================================ --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-[#1a365d]">Dashboard KPI</h1>
            <p class="text-sm text-gray-500 mt-1 font-medium">
                Ringkasan performa pelatihan periode
                <span class="font-bold text-blue-600">{{ $start->format('d M Y') }}</span>
                &ndash;
                <span class="font-bold text-blue-600">{{ $end->format('d M Y') }}</span>
            </p>
        </div>

        <form method="GET" action="{{ route('dashboard') }}" x-data="{ range: '{{ $range }}' }" class="flex flex-wrap items-center gap-2 bg-white border border-gray-100 shadow-sm rounded-2xl p-2">
            <select name="range" x-model="range" onchange="if(this.value!=='custom'){this.form.submit()}"
                    class="text-sm font-bold text-gray-700 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 focus:outline-none focus:border-blue-500 cursor-pointer">
                <option value="7hari" {{ $range==='7hari' ? 'selected' : '' }}>7 Hari Terakhir</option>
                <option value="30hari" {{ $range==='30hari' ? 'selected' : '' }}>30 Hari Terakhir</option>
                <option value="bulan_ini" {{ $range==='bulan_ini' ? 'selected' : '' }}>Bulan Ini</option>
                <option value="tahun_ini" {{ $range==='tahun_ini' ? 'selected' : '' }}>Tahun Ini</option>
                <option value="semua" {{ $range==='semua' ? 'selected' : '' }}>Semua Waktu</option>
                <option value="custom" {{ $range==='custom' ? 'selected' : '' }}>Custom...</option>
            </select>

            <template x-if="range === 'custom'">
                <div class="flex items-center gap-2">
                    <input type="date" name="start" value="{{ $start->format('Y-m-d') }}" class="text-sm font-semibold text-gray-700 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 focus:outline-none focus:border-blue-500">
                    <span class="text-gray-400 font-bold">&ndash;</span>
                    <input type="date" name="end" value="{{ $end->format('Y-m-d') }}" class="text-sm font-semibold text-gray-700 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 focus:outline-none focus:border-blue-500">
                    <button type="submit" class="text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl px-4 py-2.5 transition-all">Terapkan</button>
                </div>
            </template>
        </form>
    </div>

    {{-- ============================================ --}}
    {{-- KARTU KPI --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-4">

        {{-- Total Event --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:-translate-y-1 transition-transform">
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <p class="text-2xl font-extrabold text-[#1a365d]">{{ number_format($totalEvent) }}</p>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mt-1">Total Event</p>
        </div>

        {{-- Total Peserta --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:-translate-y-1 transition-transform">
            <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <p class="text-2xl font-extrabold text-[#1a365d]">{{ number_format($totalPeserta) }}</p>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mt-1">Total Peserta</p>
            <p class="text-[11px] font-semibold text-emerald-600 mt-1">{{ number_format($pesertaDiterima) }} diterima</p>
        </div>

        {{-- Pendapatan --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:-translate-y-1 transition-transform">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-xl font-extrabold text-[#1a365d]">Rp {{ number_format($totalPendapatan,0,',','.') }}</p>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mt-1">Pendapatan Terkumpul</p>
            @if($totalTagihan > 0)
                <div class="w-full bg-gray-100 rounded-full h-1.5 mt-2">
                    <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ min(100, round($totalPendapatan/$totalTagihan*100)) }}%"></div>
                </div>
                <p class="text-[11px] font-semibold text-gray-400 mt-1">Piutang Rp {{ number_format($totalPiutang,0,',','.') }}</p>
            @endif
        </div>

        {{-- Tingkat Kehadiran --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:-translate-y-1 transition-transform">
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-2xl font-extrabold text-[#1a365d]">{{ $persenKehadiran }}%</p>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mt-1">Tingkat Kehadiran</p>
            <p class="text-[11px] font-semibold text-gray-400 mt-1">{{ number_format($totalKehadiran) }} kehadiran tercatat</p>
        </div>

        {{-- Evaluasi & IKM --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:-translate-y-1 transition-transform">
            <div class="w-11 h-11 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
            </div>
            <p class="text-2xl font-extrabold text-[#1a365d]">{{ $rataEvaluasi !== null ? $rataEvaluasi.'%' : '-' }}</p>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mt-1">Rata-rata Evaluasi</p>
            <p class="text-[11px] font-semibold text-gray-400 mt-1">IKM: {{ $ikm !== null ? $ikm : '-' }}</p>
        </div>

    </div>

    {{-- ============================================ --}}
    {{-- BARIS CHART 1: Tren Peserta & Pendapatan + Status Pembayaran --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
        <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-extrabold text-[#1a365d]">Tren Peserta &amp; Pendapatan</h2>
                <span class="text-[10px] font-bold text-gray-400 uppercase">Per Bulan</span>
            </div>
            <canvas id="chartTren" height="110"></canvas>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h2 class="font-extrabold text-[#1a365d] mb-4">Status Pembayaran</h2>
            <canvas id="chartBayar" height="200"></canvas>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- BARIS CHART 2: Event per Tipe, Status Pendaftaran, Top Pelatihan --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h2 class="font-extrabold text-[#1a365d] mb-4">Event per Tipe Pelatihan</h2>
            <canvas id="chartTipe" height="200"></canvas>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h2 class="font-extrabold text-[#1a365d] mb-4">Status Pendaftaran Peserta</h2>
            <canvas id="chartDaftar" height="200"></canvas>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h2 class="font-extrabold text-[#1a365d] mb-4">Top 5 Pelatihan Terpopuler</h2>
            <canvas id="chartTop" height="200"></canvas>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- KALENDER EVENT --}}
    {{-- ============================================ --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-extrabold text-[#1a365d]">Kalender Event</h2>
            <div class="flex items-center gap-3 text-[11px] font-bold text-gray-500">
                <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-blue-600 inline-block"></span> Workshop</span>
                <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-violet-600 inline-block"></span> Webinar</span>
                <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-emerald-600 inline-block"></span> Pelatihan</span>
                <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-amber-600 inline-block"></span> Seminar</span>
            </div>
        </div>
        <div id="calendarEvent"></div>
    </div>

    {{-- ============================================ --}}
    {{-- TABEL: EVENT & PENGADUAN --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">

        {{-- Tabel Event --}}
        <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-5 overflow-hidden">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-extrabold text-[#1a365d]">Event Terbaru pada Periode Ini</h2>
                <a href="{{ route('event.index') }}" class="text-xs font-bold text-blue-600 hover:underline">Lihat Semua &rarr;</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-[11px] font-bold text-gray-400 uppercase border-b border-gray-100">
                            <th class="py-2 pr-3">Nama Event</th>
                            <th class="py-2 pr-3">Tipe</th>
                            <th class="py-2 pr-3">Tanggal</th>
                            <th class="py-2 pr-3 text-center">Peserta</th>
                            <th class="py-2 pr-3 text-right">Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($eventTable as $row)
                            <tr class="hover:bg-gray-50/70">
                                <td class="py-3 pr-3 font-bold text-gray-700">{{ $row['nama_event'] }}
                                    <p class="text-[11px] font-medium text-gray-400">{{ $row['pelatihan'] }}</p>
                                </td>
                                <td class="py-3 pr-3">
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700">{{ $row['tipe'] }}</span>
                                </td>
                                <td class="py-3 pr-3 text-gray-500 text-[13px] font-medium">
                                    {{ $row['tanggal_mulai']->format('d M Y') }}
                                </td>
                                <td class="py-3 pr-3 text-center font-bold text-gray-700">{{ $row['diterima'] }}/{{ $row['jumlah_peserta'] }}</td>
                                <td class="py-3 pr-3 text-right font-bold text-emerald-600">Rp {{ number_format($row['pendapatan'],0,',','.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-8 text-center text-gray-400 font-medium">Belum ada event pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tabel Pengaduan --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-extrabold text-[#1a365d]">Pengaduan Terbaru</h2>
                <a href="{{ route('pengaduan.index') }}" class="text-xs font-bold text-blue-600 hover:underline">Lihat Semua &rarr;</a>
            </div>
            <div class="space-y-3">
                @forelse($pengaduanTable as $p)
                    @php
                        $warna = [
                            'Pending' => 'bg-amber-50 text-amber-700',
                            'Diproses' => 'bg-blue-50 text-blue-700',
                            'Selesai' => 'bg-emerald-50 text-emerald-700',
                        ][$p->status] ?? 'bg-gray-50 text-gray-700';
                    @endphp
                    <div class="p-3 rounded-xl border border-gray-100 hover:bg-gray-50/70 transition-colors">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-bold text-gray-700 truncate pr-2">{{ $p->nama_lengkap }}</p>
                            <span class="shrink-0 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $warna }}">{{ $p->status }}</span>
                        </div>
                        <p class="text-[12px] text-gray-400 font-medium mt-0.5 line-clamp-1">{{ $p->tempat_kejadian }}</p>
                        <p class="text-[10px] text-gray-300 font-semibold mt-1">{{ $p->created_at->diffForHumans() }}</p>
                    </div>
                @empty
                    <p class="text-center text-gray-400 font-medium py-8">Tidak ada pengaduan.</p>
                @endforelse
            </div>
        </div>
    </div>

</div>

{{-- ============================================ --}}
{{-- CDN: Chart.js & FullCalendar --}}
{{-- ============================================ --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/locales-all.global.min.js"></script>

<script>
(function () {
    const rupiah = (v) => 'Rp ' + Number(v).toLocaleString('id-ID');
    const warnaBiru = '#1ba1e2', warnaHijau = '#059669', warnaMerah = '#ef4444';
    const paletteDonut = ['#f59e0b', '#3b82f6', '#10b981', '#8b5cf6', '#ef4444'];

    // ---------- Tren Peserta & Pendapatan ----------
    new Chart(document.getElementById('chartTren'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($bulanLabels, JSON_HEX_TAG) !!},
            datasets: [
                {
                    type: 'line',
                    label: 'Pendapatan',
                    data: {!! json_encode($bulanPendapatan) !!},
                    borderColor: warnaHijau,
                    backgroundColor: warnaHijau,
                    yAxisID: 'y1',
                    tension: 0.35,
                    fill: false,
                    pointRadius: 3,
                },
                {
                    type: 'bar',
                    label: 'Jumlah Peserta',
                    data: {!! json_encode($bulanPeserta) !!},
                    backgroundColor: 'rgba(27,161,226,0.55)',
                    borderRadius: 6,
                    yAxisID: 'y',
                }
            ]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, font: { weight: 'bold' } } },
                tooltip: { callbacks: { label: (ctx) => ctx.dataset.label === 'Pendapatan' ? rupiah(ctx.raw) : ctx.raw + ' peserta' } }
            },
            scales: {
                y: { beginAtZero: true, position: 'left', title: { display: true, text: 'Peserta' }, grid: { display: false } },
                y1: { beginAtZero: true, position: 'right', title: { display: true, text: 'Pendapatan (Rp)' }, grid: { display: false } },
            }
        }
    });

    // ---------- Status Pembayaran (Donut) ----------
    new Chart(document.getElementById('chartBayar'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($statusBayarLabel, JSON_HEX_TAG) !!},
            datasets: [{ data: {!! json_encode($statusBayar->values()) !!}, backgroundColor: paletteDonut, borderWidth: 0 }]
        },
        options: { plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, font: { weight: 'bold', size: 11 } } } }, cutout: '65%' }
    });

    // ---------- Event per Tipe (Bar) ----------
    new Chart(document.getElementById('chartTipe'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($tipeLabel, JSON_HEX_TAG) !!},
            datasets: [{ data: {!! json_encode($eventByTipe->values()) !!}, backgroundColor: warnaBiru, borderRadius: 8, maxBarThickness: 42 }]
        },
        options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
    });

    // ---------- Status Pendaftaran (Donut) ----------
    new Chart(document.getElementById('chartDaftar'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($statusDaftarLabel, JSON_HEX_TAG) !!},
            datasets: [{ data: {!! json_encode($statusDaftar->values()) !!}, backgroundColor: ['#f59e0b', '#10b981', '#ef4444'], borderWidth: 0 }]
        },
        options: { plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, font: { weight: 'bold', size: 11 } } } }, cutout: '65%' }
    });

    // ---------- Top 5 Pelatihan (Horizontal Bar) ----------
    new Chart(document.getElementById('chartTop'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($topPelatihan->pluck('nama_pelatihan'), JSON_HEX_TAG) !!},
            datasets: [{ data: {!! json_encode($topPelatihan->pluck('jumlah')) !!}, backgroundColor: '#7c3aed', borderRadius: 8 }]
        },
        options: { indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true, ticks: { precision: 0 } } } }
    });

    // ---------- Kalender Event ----------
    document.addEventListener('DOMContentLoaded', function () {
        var calendarEl = document.getElementById('calendarEvent');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            height: 620,
            locale: 'id',
            headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,listMonth' },
            events: {!! json_encode($calendarEvents, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) !!},
            eventDidMount: function (info) {
                if (info.event.extendedProps.pelatihan) {
                    info.el.setAttribute('title', info.event.title + ' — ' + info.event.extendedProps.pelatihan);
                }
            }
        });
        calendar.render();
    });
})();
</script>
@endsection
