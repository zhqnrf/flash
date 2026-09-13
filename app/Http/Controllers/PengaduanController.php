<?php
namespace App\Http\Controllers;

use App\Models\Pengaduan;
use Illuminate\Http\Request;

class PengaduanController extends Controller
{
    // Halaman Form Publik (User)
    public function create()
    {
        return view('public.pengaduan.form');
    }
// Halaman Cetak PDF Laporan Pengaduan
    public function cetakPdf(Request $request)
    {
        $status = $request->input('status');
        $pengaduans = Pengaduan::when($status, function ($q, $status) {
                return $q->where('status', $status);
            })->latest()->get();

        return view('pengaduan.cetak', compact('pengaduans', 'status'));
    }
// Halaman Cetak Detail per Laporan
    public function cetakDetail(Pengaduan $pengaduan)
    {
        return view('pengaduan.cetak-detail', compact('pengaduan'));
    }
    // Export Excel Sederhana (.xls / CSV headers)
    public function exportExcel(Request $request)
    {
        $fileName = 'Laporan_Pengaduan_SIMPEL_' . date('Y-m-d') . '.xls';
        $pengaduans = Pengaduan::latest()->get();

        $headers = array(
            "Content-type"        => "application/vnd.ms-excel",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $callback = function() use($pengaduans) {
            $file = fopen('php://output', 'w');
            fputcsv($file, array('ID', 'Tanggal', 'Nama Pelapor', 'Email', 'No WhatsApp', 'Tempat Kejadian', 'Isi Pengaduan', 'Kritik & Saran', 'Status', 'Jawaban Admin'));

            foreach ($pengaduans as $p) {
                fputcsv($file, array(
                    $p->id,
                    $p->created_at,
                    $p->nama_lengkap,
                    $p->email,
                    $p->no_whatsapp,
                    $p->tempat_kejadian,
                    $p->isi_pengaduan,
                    $p->kritik_saran,
                    $p->status,
                    $p->jawaban_admin
                ));
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    // Proses Simpan Pengaduan dari User
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'nama_lengkap' => 'required|string|max:255',
            'no_whatsapp' => 'required|string|max:20',
            'tempat_kejadian' => 'required|string|max:255',
            'isi_pengaduan' => 'required|string',
            'kritik_saran' => 'nullable|string',
        ]);

        Pengaduan::create($request->all());

        return redirect()->route('public.pengaduan')->with('success', 'Terima kasih! Pengaduan dan masukan Anda berhasil dikirimkan.');
    }

    // Halaman Rekap Admin (Dilengkapi Search & Filter Status)
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $pengaduans = Pengaduan::when($search, function ($query, $search) {
                return $query->where('nama_lengkap', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%")
                             ->orWhere('tempat_kejadian', 'like', "%{$search}%")
                             ->orWhere('isi_pengaduan', 'like', "%{$search}%");
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('pengaduan.index', compact('pengaduans'));
    }

    // Proses Admin Menjawab / Update Status Pengaduan
    public function update(Request $request, Pengaduan $pengaduan)
    {
        $request->validate([
            'status' => 'required|in:Pending,Diproses,Selesai',
            'jawaban_admin' => 'required|string',
        ]);

        $pengaduan->update([
            'status' => $request->status,
            'jawaban_admin' => $request->jawaban_admin,
            'tanggal_dijawab' => now(),
        ]);

        return redirect()->route('pengaduan.index')->with('success', 'Tindak lanjut pengaduan berhasil disimpan!');
    }

    // Hapus Pengaduan
    public function destroy(Pengaduan $pengaduan)
    {
        $pengaduan->delete();
        return redirect()->route('pengaduan.index')->with('success', 'Data pengaduan berhasil dihapus.');
    }
}