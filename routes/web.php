<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\EvaluasiPublicController;
use App\Http\Controllers\EvaluasiRekapController;
use App\Http\Controllers\PenilaianSkillController;
use App\Http\Controllers\PenilaianSkillRekapController;
use App\Http\Controllers\SertifikatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/
// Rute Publik (Peserta)
Route::get('/registrasi/{uuid}', [\App\Http\Controllers\RegistrasiController::class, 'create'])->name('registrasi.public');
Route::post('/registrasi/{uuid}', [\App\Http\Controllers\RegistrasiController::class, 'store'])->name('registrasi.store');

// Rute Publik: Pelunasan Cicilan (dikirim admin ke peserta yang statusnya "Cicil")
Route::get('/pembayaran/{uuid}', [\App\Http\Controllers\RegistrasiController::class, 'showPembayaran'])->name('pembayaran.public');
Route::post('/pembayaran/{uuid}', [\App\Http\Controllers\RegistrasiController::class, 'storePembayaran'])->name('pembayaran.store');

// Rute Publik: Absensi (cari nama + selfie, dibatasi jam presensi event)
Route::get('/presensi/{uuid}', [AbsensiController::class, 'create'])->name('presensi.public');
Route::post('/presensi/{uuid}', [AbsensiController::class, 'store'])->name('presensi.store');
// Manajemen Event
Route::get('/pengaduan', [\App\Http\Controllers\PengaduanController::class, 'create'])->name('public.pengaduan');
Route::post('/pengaduan', [\App\Http\Controllers\PengaduanController::class, 'store'])->name('pengaduan.store');

// Rute Publik: Evaluasi Pelatihan (oleh peserta) + daftar link evaluasi pemateri
Route::get('/evaluasi-pelatihan/{uuid}', [EvaluasiPublicController::class, 'create'])->name('evaluasi-pelatihan.public');
Route::post('/evaluasi-pelatihan/{uuid}', [EvaluasiPublicController::class, 'store'])->name('evaluasi-pelatihan.store');

// Rute Publik: Evaluasi Fasilitator (khusus 1 pemateri, per event)
Route::get('/evaluasi-fasilitator/{uuid}/{fasilitator}', [EvaluasiPublicController::class, 'createFasilitator'])->name('evaluasi-fasilitator.public');
Route::post('/evaluasi-fasilitator/{uuid}/{fasilitator}', [EvaluasiPublicController::class, 'storeFasilitator'])->name('evaluasi-fasilitator.store');

// Rute Admin: hapus/reset evaluasi pelatihan seorang peserta
Route::delete('/evaluasi-peserta/{registrasi}/hapus', [EvaluasiPublicController::class, 'hapusEvaluasiPelatihan'])->name('evaluasi-peserta.hapus');

// ==========================================
// Rute Publik: Penilaian Skill Peserta oleh Fasilitator (per materi)
// Fasilitator memilih peserta lalu mengisi nilai per skill (rubrik)
// ==========================================
Route::get('/penilaian-skill/{uuid}/{fasilitator}/{materi}', [PenilaianSkillController::class, 'create'])->name('penilaian-skill.public');
Route::post('/penilaian-skill/{uuid}/{fasilitator}/{materi}', [PenilaianSkillController::class, 'store'])->name('penilaian-skill.store');

// ==========================================
// Rute Publik: Sertifikat Peserta (cetak & validasi QR)
// ==========================================
Route::get('/sertifikat/{uuid}/{registrasi}', [SertifikatController::class, 'cetak'])->name('sertifikat.cetak');
Route::get('/validasi-sertifikat/{uuid}/{registrasi}', [SertifikatController::class, 'validasi'])->name('sertifikat.validasi');

// Rute Publik: Survey Kepuasan Masyarakat (SKM)
Route::get('/survey-kepuasan', [\App\Http\Controllers\SurveyKepuasanController::class, 'create'])->name('survey-kepuasan.public');
Route::post('/survey-kepuasan', [\App\Http\Controllers\SurveyKepuasanController::class, 'store'])->name('survey-kepuasan.store');
// Rute Admin: Rekap & Perhitungan IKM
Route::get('/survey-kepuasan/rekap', [\App\Http\Controllers\SurveyKepuasanController::class, 'rekap'])->name('survey-kepuasan.rekap');

// Route baru untuk Cetak via JS & Halaman Validasi QR
Route::get('/survey-kepuasan/cetak', [\App\Http\Controllers\SurveyKepuasanController::class, 'cetak'])->name('survey-kepuasan.cetak');
Route::get('/validasi-ikm/{tahun}/{bulan?}', [\App\Http\Controllers\SurveyKepuasanController::class, 'validasi'])->name('validasi.ikm');
// Route Landing Page
Route::get('/landing', [LandingController::class, 'index']);
// Rute Validasi Dokumen Event
Route::get('/validasi-dokumen/event/{uuid}', [\App\Http\Controllers\LandingController::class, 'validasiEvent'])->name('validasi.event');
// ==========================================
// ROUTE AUTENTIKASI (LOGIN & LOGOUT)
// ==========================================
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==========================================
// ROUTE DASHBOARD (KPI)
// ==========================================
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// ==========================================
// ROUTE NOTIFIKASI (Topbar Admin)
// ==========================================
Route::prefix('notifikasi')->name('notifikasi.')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::post('/baca-semua', [NotificationController::class, 'readAll'])->name('baca-semua');
    Route::get('/{notifikasi}/baca', [NotificationController::class, 'read'])->name('baca');
});

// Rute Admin Pengaduan
Route::prefix('admin/pengaduan')->name('pengaduan.')->group(function () {
    Route::get('/', [\App\Http\Controllers\PengaduanController::class, 'index'])->name('index');
    Route::get('/cetak-pdf', [\App\Http\Controllers\PengaduanController::class, 'cetakPdf'])->name('cetak-pdf');
    Route::get('/export-excel', [\App\Http\Controllers\PengaduanController::class, 'exportExcel'])->name('export-excel');
    Route::put('/{pengaduan}', [\App\Http\Controllers\PengaduanController::class, 'update'])->name('update');
    Route::get('/{pengaduan}/cetak-detail', [\App\Http\Controllers\PengaduanController::class, 'cetakDetail'])->name('cetak-detail');
    Route::delete('/{pengaduan}', [\App\Http\Controllers\PengaduanController::class, 'destroy'])->name('destroy');
});
Route::prefix('event')->name('event.')->group(function () {
    Route::get('/', [\App\Http\Controllers\EventController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\EventController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\EventController::class, 'store'])->name('store');
    // Rute Halaman Cetak Surat Bukti Event
Route::get('/{event}/cetak-surat', [\App\Http\Controllers\EventController::class, 'cetakSurat'])->name('cetak-surat');
    // Rute Edit, Update, Destroy
    Route::get('/{event}/edit', [\App\Http\Controllers\EventController::class, 'edit'])->name('edit');
    Route::put('/{event}', [\App\Http\Controllers\EventController::class, 'update'])->name('update');
    Route::delete('/{event}', [\App\Http\Controllers\EventController::class, 'destroy'])->name('destroy');

    // Endpoint AJAX
    Route::get('/ajax/materi-fasilitator/{pelatihan_id}', [\App\Http\Controllers\EventController::class, 'getMateriFasilitator'])->name('ajax.materi-fasilitator');

    // Halaman Pembayaran Peserta per Event
    Route::get('/{event}/pembayaran', [\App\Http\Controllers\PembayaranController::class, 'index'])->name('pembayaran');

    // Halaman Rekap Absensi per Event
    Route::get('/{event}/absensi', [\App\Http\Controllers\AbsensiController::class, 'rekap'])->name('absensi');
    Route::post('/{event}/status-absen', [\App\Http\Controllers\AbsensiController::class, 'updateStatus'])->name('status-absen');
    // 👇 TAMBAHKAN ROUTE TOGGLE ABSEN DI SINI (Dalam grup 'event.') 👇
    // Perhatikan name-nya hanya 'toggle-absen' karena otomatis digabung dengan 'event.' dari prefix grup
    Route::post('/{event}/toggle-absen', [\App\Http\Controllers\AbsensiController::class, 'toggleAbsen'])->name('toggle-absen');

    // Halaman Rekap Evaluasi Pelatihan per Event
    Route::get('/{event}/evaluasi', [EvaluasiRekapController::class, 'index'])->name('evaluasi');

    // Halaman Rekap/Leaderboard Penilaian Skill Peserta (oleh Fasilitator) per Event
    Route::get('/{event}/penilaian-skill', [PenilaianSkillRekapController::class, 'index'])->name('penilaian-skill');
    Route::delete('/{event}/penilaian-skill/{registrasi}/reset', [PenilaianSkillRekapController::class, 'reset'])->name('penilaian-skill.reset');
});

// 👇 TAMBAHKAN ROUTE RESET ABSENSI DI LUAR GRUP EVENT 👇
Route::delete('/absensi/{absensi}/reset', [\App\Http\Controllers\AbsensiController::class, 'resetAbsensi'])->name('absensi.reset');
Route::prefix('registrasi')->name('registrasi.')->group(function () {
    Route::post('/{registrasi}/acc', [PembayaranController::class, 'acc'])->name('acc');
    Route::post('/{registrasi}/tolak', [PembayaranController::class, 'tolak'])->name('tolak');
    Route::post('/{registrasi}/update-cicilan', [PembayaranController::class, 'updateCicilan'])->name('update-cicilan');
    Route::post('/{registrasi}/lunas', [PembayaranController::class, 'tandaiLunas'])->name('lunas');
    Route::delete('/{registrasi}/hapus', [PembayaranController::class, 'hapusPeserta'])->name('hapus');
    Route::put('/{registrasi}/quick-edit', [PembayaranController::class, 'updateQuickEdit'])->name('quick-edit');
});

// ==========================================
// ROUTE MASTER DATA
// ==========================================
Route::prefix('master')->name('master.')->group(function () {
    // Rute Master Skill Materi
    Route::prefix('skill-materi')->name('skill-materi.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SkillMateriController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\SkillMateriController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\SkillMateriController::class, 'store'])->name('store');
        
        Route::get('/export-backend', [\App\Http\Controllers\SkillMateriController::class, 'exportBackend'])->name('export');
        Route::post('/import-backend', [\App\Http\Controllers\SkillMateriController::class, 'importBackend'])->name('import');

        Route::get('/{skill_materi}/edit', [\App\Http\Controllers\SkillMateriController::class, 'edit'])->name('edit');
        Route::put('/{skill_materi}', [\App\Http\Controllers\SkillMateriController::class, 'update'])->name('update');
        Route::delete('/{skill_materi}', [\App\Http\Controllers\SkillMateriController::class, 'destroy'])->name('destroy');
    });
    // Rute Master Fasilitator (Manual tanpa resource)
    Route::prefix('fasilitator')->name('fasilitator.')->group(function () {
        Route::get('/', [App\Http\Controllers\FasilitatorController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\FasilitatorController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\FasilitatorController::class, 'store'])->name('store');
        
        // Rute Export & Import Backend
        Route::get('/export-backend', [App\Http\Controllers\FasilitatorController::class, 'exportBackend'])->name('export');
        Route::post('/import-backend', [App\Http\Controllers\FasilitatorController::class, 'importBackend'])->name('import');

        Route::get('/{fasilitator}/edit', [App\Http\Controllers\FasilitatorController::class, 'edit'])->name('edit');
        Route::put('/{fasilitator}', [App\Http\Controllers\FasilitatorController::class, 'update'])->name('update');
        Route::delete('/{fasilitator}', [App\Http\Controllers\FasilitatorController::class, 'destroy'])->name('destroy');
    });
    Route::prefix('pelatihan')->name('pelatihan.')->group(function () {
        Route::get('/export-backend', [\App\Http\Controllers\PelatihanController::class, 'exportBackend'])->name('export');
        Route::post('/import-backend', [\App\Http\Controllers\PelatihanController::class, 'importBackend'])->name('import');
        Route::get('/', [\App\Http\Controllers\PelatihanController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\PelatihanController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\PelatihanController::class, 'store'])->name('store');
        Route::get('/{pelatihan}/edit', [\App\Http\Controllers\PelatihanController::class, 'edit'])->name('edit');
        Route::put('/{pelatihan}', [\App\Http\Controllers\PelatihanController::class, 'update'])->name('update');
        Route::delete('/{pelatihan}', [\App\Http\Controllers\PelatihanController::class, 'destroy'])->name('destroy');
    });
   Route::prefix('evaluasi-pelatihan')->name('evaluasi-pelatihan.')->group(function () {
        Route::get('/', [\App\Http\Controllers\EvaluasiPelatihanController::class, 'index'])->name('index');
         Route::get('/export-backend', [\App\Http\Controllers\EvaluasiPelatihanController::class, 'exportBackend'])->name('export');
    Route::post('/import-backend', [\App\Http\Controllers\EvaluasiPelatihanController::class, 'importBackend'])->name('import');
        Route::get('/create', [\App\Http\Controllers\EvaluasiPelatihanController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\EvaluasiPelatihanController::class, 'store'])->name('store');
        Route::get('/{evaluasi_pelatihan}/edit', [\App\Http\Controllers\EvaluasiPelatihanController::class, 'edit'])->name('edit');
        Route::put('/{evaluasi_pelatihan}', [\App\Http\Controllers\EvaluasiPelatihanController::class, 'update'])->name('update');
        Route::delete('/{evaluasi_pelatihan}', [\App\Http\Controllers\EvaluasiPelatihanController::class, 'destroy'])->name('destroy');
    });
    // ------------------------------------------
    // 4. Master Evaluasi Fasilitator
    // ------------------------------------------
    Route::prefix('evaluasi-fasilitator')->name('evaluasi-fasilitator.')->group(function () {
        // Rute Master Evaluasi Materi
  
    // Rute Master Evaluasi Fasilitator
    Route::get('/export-backend', [\App\Http\Controllers\EvaluasiFasilitatorController::class, 'exportBackend'])->name('export');
    Route::post('/import-backend', [\App\Http\Controllers\EvaluasiFasilitatorController::class, 'importBackend'])->name('import');
        Route::get('/', [\App\Http\Controllers\EvaluasiFasilitatorController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\EvaluasiFasilitatorController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\EvaluasiFasilitatorController::class, 'store'])->name('store');
        Route::get('/{evaluasi_fasilitator}/edit', [\App\Http\Controllers\EvaluasiFasilitatorController::class, 'edit'])->name('edit');
        Route::put('/{evaluasi_fasilitator}', [\App\Http\Controllers\EvaluasiFasilitatorController::class, 'update'])->name('update');
        Route::delete('/{evaluasi_fasilitator}', [\App\Http\Controllers\EvaluasiFasilitatorController::class, 'destroy'])->name('destroy');
    });
    // ------------------------------------------
    // 2. Master Evaluasi Materi
    // ------------------------------------------
    Route::prefix('evaluasi-materi')->name('evaluasi-materi.')->group(function () {
          Route::get('/export-backend', [\App\Http\Controllers\EvaluasiMateriController::class, 'exportBackend'])->name('export');
    Route::post('/import-backend', [\App\Http\Controllers\EvaluasiMateriController::class, 'importBackend'])->name('import');

        Route::get('/', [\App\Http\Controllers\EvaluasiMateriController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\EvaluasiMateriController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\EvaluasiMateriController::class, 'store'])->name('store');
        Route::get('/{evaluasi_materi}/edit', [\App\Http\Controllers\EvaluasiMateriController::class, 'edit'])->name('edit');
        Route::put('/{evaluasi_materi}', [\App\Http\Controllers\EvaluasiMateriController::class, 'update'])->name('update');
        Route::delete('/{evaluasi_materi}', [\App\Http\Controllers\EvaluasiMateriController::class, 'destroy'])->name('destroy');
    });

});

// ==========================================
// ROUTE MANAJEMEN AKUN (USERS)
// ==========================================
Route::prefix('users')->name('users.')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::get('/create', [UserController::class, 'create'])->name('create');
    Route::post('/', [UserController::class, 'store'])->name('store');
    Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
    Route::put('/{user}', [UserController::class, 'update'])->name('update');
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
});