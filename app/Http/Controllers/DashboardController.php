<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registrasi;
use App\Models\Absensi;
use App\Models\Pengaduan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // Nama bulan Indonesia (server locale belum tentu 'id', jadi kita mapping manual)
    protected array $namaBulan = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
        7 => 'Jul', 8 => 'Ags', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
    ];

    public function index(Request $request)
    {
        [$range, $start, $end] = $this->resolveRentang($request);

        // ==========================================
        // Event yang tanggal_mulai-nya jatuh di dalam rentang terpilih
        // ==========================================
        $eventIds = Event::whereBetween('tanggal_mulai', [$start->toDateString(), $end->toDateString()])
            ->pluck('id');

        $totalEvent = $eventIds->count();

        // ==========================================
        // KPI: Peserta & Pendaftaran
        // ==========================================
        $registrasiQuery = Registrasi::whereIn('event_id', $eventIds);

        $totalPeserta = (clone $registrasiQuery)->count();
        $pesertaDiterima = (clone $registrasiQuery)->where('status_pendaftaran', 'Diterima')->count();
        $pesertaMenunggu = (clone $registrasiQuery)->where('status_pendaftaran', 'Menunggu')->count();
        $pesertaDitolak = (clone $registrasiQuery)->where('status_pendaftaran', 'Ditolak')->count();

        // ==========================================
        // KPI: Keuangan (pendapatan terkumpul vs total tagihan)
        // ==========================================
        $keuangan = DB::table('registrasis')
            ->join('events', 'events.id', '=', 'registrasis.event_id')
            ->whereIn('registrasis.event_id', $eventIds)
            ->selectRaw('COALESCE(SUM(registrasis.total_dibayar),0) as terkumpul, COALESCE(SUM(events.biaya_pelatihan),0) as tagihan')
            ->first();

        $totalPendapatan = (int) ($keuangan->terkumpul ?? 0);
        $totalTagihan = (int) ($keuangan->tagihan ?? 0);
        $totalPiutang = max($totalTagihan - $totalPendapatan, 0);

        // ==========================================
        // KPI: Kehadiran (partisipasi absensi peserta yang diterima)
        // ==========================================
        $registrasiIds = (clone $registrasiQuery)->pluck('id');
        $totalKehadiran = Absensi::whereIn('registrasi_id', $registrasiIds)
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->count();
        $persenKehadiran = $pesertaDiterima > 0 ? round(($totalKehadiran / $pesertaDiterima) * 100, 1) : 0;

        // ==========================================
        // KPI: Rata-rata nilai Evaluasi Pelatihan (dinormalisasi ke skala 0-100)
        // ==========================================
        $rataEvaluasi = DB::table('evaluasi_pelatihan_jawabans')
            ->join('evaluasi_pelatihans', 'evaluasi_pelatihans.id', '=', 'evaluasi_pelatihan_jawabans.evaluasi_pelatihan_id')
            ->whereIn('evaluasi_pelatihan_jawabans.event_id', $eventIds)
            ->selectRaw('AVG(evaluasi_pelatihan_jawabans.nilai / NULLIF(evaluasi_pelatihans.rentang_nilai_max,0) * 100) as rata')
            ->value('rata');
        $rataEvaluasi = $rataEvaluasi !== null ? round($rataEvaluasi, 1) : null;

        // ==========================================
        // KPI: Indeks Kepuasan Masyarakat (IKM) dari Survey Kepuasan
        // Skala jawaban 1-4 -> IKM = rata-rata x 25 (skala 25-100, standar Permenpan)
        // ==========================================
        $rataSurvey = DB::table('survey_kepuasan_jawabans')
            ->join('survey_kepuasans', 'survey_kepuasans.id', '=', 'survey_kepuasan_jawabans.survey_kepuasan_id')
            ->whereBetween('survey_kepuasans.tanggal_survey', [$start->toDateString(), $end->toDateString()])
            ->avg('survey_kepuasan_jawabans.nilai');
        $ikm = $rataSurvey ? round($rataSurvey * 25, 1) : null;

        // ==========================================
        // KPI: Pengaduan (berdasarkan tanggal masuk)
        // ==========================================
        $pengaduanQuery = Pengaduan::whereBetween('created_at', [$start, $end]);
        $totalPengaduan = (clone $pengaduanQuery)->count();
        $pengaduanByStatusRaw = (clone $pengaduanQuery)
            ->select('status', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('status')
            ->pluck('jumlah', 'status');
        $statusPengaduanLabel = ['Pending', 'Diproses', 'Selesai'];
        $pengaduanByStatus = collect($statusPengaduanLabel)->map(fn ($s) => (int) ($pengaduanByStatusRaw[$s] ?? 0));

        // ==========================================
        // CHART: Event berdasarkan tipe pelatihan
        // ==========================================
        $tipeLabel = ['Workshop', 'Webinar', 'Pelatihan', 'Seminar'];
        $eventByTipeRaw = Event::whereIn('id', $eventIds)
            ->select('tipe_pelatihan', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('tipe_pelatihan')
            ->pluck('jumlah', 'tipe_pelatihan');
        $eventByTipe = collect($tipeLabel)->map(fn ($t) => (int) ($eventByTipeRaw[$t] ?? 0));

        // ==========================================
        // CHART: Status pembayaran & status pendaftaran peserta
        // ==========================================
        $statusBayarLabel = ['Belum Bayar', 'Cicil', 'Lunas'];
        $statusBayarRaw = (clone $registrasiQuery)
            ->select('status_pembayaran', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('status_pembayaran')
            ->pluck('jumlah', 'status_pembayaran');
        $statusBayar = collect($statusBayarLabel)->map(fn ($s) => (int) ($statusBayarRaw[$s] ?? 0));

        $statusDaftarLabel = ['Menunggu', 'Diterima', 'Ditolak'];
        $petaStatusDaftar = [
            'Menunggu' => $pesertaMenunggu,
            'Diterima' => $pesertaDiterima,
            'Ditolak' => $pesertaDitolak,
        ];
        $statusDaftar = collect($statusDaftarLabel)->map(fn ($s) => $petaStatusDaftar[$s]);

        // ==========================================
        // CHART: Tren peserta & pendapatan per bulan sepanjang rentang
        // ==========================================
        $monthlyRaw = DB::table('registrasis')
            ->join('events', 'events.id', '=', 'registrasis.event_id')
            ->whereIn('registrasis.event_id', $eventIds)
            ->selectRaw("DATE_FORMAT(events.tanggal_mulai, '%Y-%m') as bulan, COUNT(registrasis.id) as jumlah_peserta, COALESCE(SUM(registrasis.total_dibayar),0) as pendapatan")
            ->groupBy('bulan')
            ->get()
            ->keyBy('bulan');

        $bulanLabels = [];
        $bulanPeserta = [];
        $bulanPendapatan = [];
        $cursor = $start->copy()->startOfMonth();
        $batasAkhir = $end->copy()->startOfMonth();
        // Batasi maksimal 24 titik agar chart tetap enak dibaca
        $pengaman = 0;
        while ($cursor->lte($batasAkhir) && $pengaman < 24) {
            $key = $cursor->format('Y-m');
            $bulanLabels[] = ($this->namaBulan[$cursor->month] ?? $cursor->format('M')) . ' ' . $cursor->format('y');
            $bulanPeserta[] = (int) ($monthlyRaw[$key]->jumlah_peserta ?? 0);
            $bulanPendapatan[] = (int) ($monthlyRaw[$key]->pendapatan ?? 0);
            $cursor->addMonth();
            $pengaman++;
        }

        // ==========================================
        // CHART: Top 5 pelatihan berdasarkan jumlah peserta
        // ==========================================
        $topPelatihan = DB::table('registrasis')
            ->join('events', 'events.id', '=', 'registrasis.event_id')
            ->join('pelatihans', 'pelatihans.id', '=', 'events.pelatihan_id')
            ->whereIn('registrasis.event_id', $eventIds)
            ->select('pelatihans.nama_pelatihan', DB::raw('COUNT(registrasis.id) as jumlah'))
            ->groupBy('pelatihans.nama_pelatihan')
            ->orderByDesc('jumlah')
            ->limit(5)
            ->get();

        // ==========================================
        // TABEL: Event dalam rentang terpilih (lengkap dengan jumlah peserta & pendapatan)
        // ==========================================
        $eventTable = Event::with('pelatihan')
            ->whereIn('id', $eventIds)
            ->orderByDesc('tanggal_mulai')
            ->limit(8)
            ->get()
            ->map(function ($event) {
                $reg = Registrasi::where('event_id', $event->id);
                return [
                    'nama_event' => $event->nama_event,
                    'pelatihan' => optional($event->pelatihan)->nama_pelatihan ?? '-',
                    'tipe' => $event->tipe_pelatihan,
                    'tanggal_mulai' => Carbon::parse($event->tanggal_mulai),
                    'tanggal_selesai' => Carbon::parse($event->tanggal_selesai),
                    'jumlah_peserta' => (clone $reg)->count(),
                    'diterima' => (clone $reg)->where('status_pendaftaran', 'Diterima')->count(),
                    'pendapatan' => (clone $reg)->sum('total_dibayar'),
                ];
            });

        // ==========================================
        // TABEL: Pengaduan terbaru
        // ==========================================
        $pengaduanTable = Pengaduan::orderByDesc('created_at')->limit(6)->get();

        // ==========================================
        // KALENDER: Semua event (tidak terikat filter rentang, agar kalender tetap informatif)
        // ==========================================
        $warnaTipe = [
            'Workshop' => '#2563eb',
            'Webinar' => '#7c3aed',
            'Pelatihan' => '#059669',
            'Seminar' => '#d97706',
        ];
        $calendarEvents = Event::with('pelatihan')->get()->map(function ($event) use ($warnaTipe) {
            return [
                'title' => $event->nama_event,
                'start' => Carbon::parse($event->tanggal_mulai)->format('Y-m-d'),
                'end' => Carbon::parse($event->tanggal_selesai)->addDay()->format('Y-m-d'), // FullCalendar: end bersifat eksklusif
                'color' => $warnaTipe[$event->tipe_pelatihan] ?? '#334155',
                'extendedProps' => [
                    'pelatihan' => optional($event->pelatihan)->nama_pelatihan ?? '-',
                    'tipe' => $event->tipe_pelatihan,
                    'sistem' => $event->sistem_pelatihan,
                    'lokasi' => $event->lokasi ?? ($event->sistem_pelatihan === 'Daring' ? 'Online' : '-'),
                ],
            ];
        });

        return view('dashboard.index', [
            'range' => $range,
            'start' => $start,
            'end' => $end,
            'totalEvent' => $totalEvent,
            'totalPeserta' => $totalPeserta,
            'pesertaDiterima' => $pesertaDiterima,
            'pesertaMenunggu' => $pesertaMenunggu,
            'pesertaDitolak' => $pesertaDitolak,
            'totalPendapatan' => $totalPendapatan,
            'totalTagihan' => $totalTagihan,
            'totalPiutang' => $totalPiutang,
            'persenKehadiran' => $persenKehadiran,
            'totalKehadiran' => $totalKehadiran,
            'rataEvaluasi' => $rataEvaluasi,
            'ikm' => $ikm,
            'totalPengaduan' => $totalPengaduan,
            'pengaduanByStatus' => $pengaduanByStatus,
            'statusPengaduanLabel' => $statusPengaduanLabel,
            'tipeLabel' => $tipeLabel,
            'eventByTipe' => $eventByTipe,
            'statusBayarLabel' => $statusBayarLabel,
            'statusBayar' => $statusBayar,
            'statusDaftarLabel' => $statusDaftarLabel,
            'statusDaftar' => $statusDaftar,
            'bulanLabels' => $bulanLabels,
            'bulanPeserta' => $bulanPeserta,
            'bulanPendapatan' => $bulanPendapatan,
            'topPelatihan' => $topPelatihan,
            'eventTable' => $eventTable,
            'pengaduanTable' => $pengaduanTable,
            'calendarEvents' => $calendarEvents,
        ]);
    }

    /**
     * Menentukan rentang tanggal aktif berdasarkan input GET (range / start / end).
     */
    protected function resolveRentang(Request $request): array
    {
        $range = $request->input('range', '30hari');
        $today = Carbon::today();

        switch ($range) {
            case '7hari':
                $start = $today->copy()->subDays(6)->startOfDay();
                $end = $today->copy()->endOfDay();
                break;
            case 'bulan_ini':
                $start = $today->copy()->startOfMonth();
                $end = $today->copy()->endOfMonth()->endOfDay();
                break;
            case 'tahun_ini':
                $start = $today->copy()->startOfYear();
                $end = $today->copy()->endOfYear()->endOfDay();
                break;
            case 'semua':
                $start = Carbon::createFromDate(2020, 1, 1)->startOfDay();
                $end = $today->copy()->addYears(2)->endOfDay();
                break;
            case 'custom':
                $start = $request->filled('start')
                    ? Carbon::parse($request->input('start'))->startOfDay()
                    : $today->copy()->subDays(29)->startOfDay();
                $end = $request->filled('end')
                    ? Carbon::parse($request->input('end'))->endOfDay()
                    : $today->copy()->endOfDay();
                break;
            case '30hari':
            default:
                $range = '30hari';
                $start = $today->copy()->subDays(29)->startOfDay();
                $end = $today->copy()->endOfDay();
                break;
        }

        if ($start->gt($end)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        return [$range, $start, $end];
    }
}
