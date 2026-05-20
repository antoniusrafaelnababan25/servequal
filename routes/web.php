<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\KuesionerMahasiswaController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboard;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\SuperAdmin\SystemSettingController;
use App\Http\Controllers\SuperAdmin\LaporanController as SuperAdminLaporan;
use App\Http\Controllers\SuperAdmin\KuesionerPeriodeController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\DosenController;
use App\Http\Controllers\Admin\MahasiswaController;
use App\Http\Controllers\Admin\PertanyaanController;
use App\Http\Controllers\Admin\KuesionerController;
use App\Http\Controllers\Admin\LaporanController as AdminLaporan;
use App\Http\Controllers\Dosen\DashboardController as DosenDashboard;
use App\Http\Controllers\Dosen\KuesionerFasilitasController;
use App\Http\Controllers\Dosen\PenilaianMahasiswaController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// =========================================================================
// 1. Halaman Publik – Langsung ke Validasi Mahasiswa
// =========================================================================
Route::get('/', [KuesionerMahasiswaController::class, 'showValidasiForm'])->name('home');

// =========================================================================
// 2. Route Mahasiswa (Tanpa Login) – Validasi & Isi Kuesioner
// =========================================================================
Route::prefix('kuesioner')->name('public.')->group(function () {
    Route::get('/mahasiswa/validasi', [KuesionerMahasiswaController::class, 'showValidasiForm'])->name('validasi.form');
    Route::post('/mahasiswa/validasi', [KuesionerMahasiswaController::class, 'validasi'])->name('validasi');
    Route::get('/mahasiswa/isi', [KuesionerMahasiswaController::class, 'isiForm'])->name('isi.form');
    Route::post('/mahasiswa/isi', [KuesionerMahasiswaController::class, 'simpan'])->name('isi.simpan');
    Route::get('/mahasiswa/selesai', [KuesionerMahasiswaController::class, 'selesai'])->name('selesai');



    // Route akses ditolak (TAMBAHKAN INI)
    Route::get('/access-denied', [KuesionerMahasiswaController::class, 'accessDenied'])->name('access.denied');
});

// =========================================================================
// 3. Route dengan Autentikasi (Semua Role yang Login)
// =========================================================================
Route::middleware(['auth'])->group(function () {

    // Dashboard redirect berdasarkan role
    Route::get('/dashboard', function (Request $request) {
        $user = $request->user();
        return match ($user->role) {
            'super_admin' => redirect()->route('super.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
            'dosen' => redirect()->route('dosen.dashboard'),
            default => view('dashboard'),
        };
    })->name('dashboard');

    // Profile routes (untuk semua role)
    Route::middleware(['auth'])->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
        Route::put('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    // =====================================================================
// 4. SUPER ADMIN (role: super_admin)
// =====================================================================
    Route::prefix('super-admin')->name('super.')->middleware('role:super_admin')->group(function () {
        Route::get('/dashboard', [SuperAdminDashboard::class, 'index'])->name('dashboard');
        Route::get('/chart-data', [SuperAdminDashboard::class, 'chartData'])->name('chart-data');

        Route::resource('users', UserController::class);
        Route::post('users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
        Route::get('users/prodi-by-jurusan/{jurusan_id}', [UserController::class, 'getProdiByJurusan'])->name('users.prodi-by-jurusan'); // DIPERBAIKI: pakai parameter URL

        Route::get('settings', [SystemSettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [SystemSettingController::class, 'update'])->name('settings.update');
        Route::post('settings/{key}', [SystemSettingController::class, 'updateSingle'])->name('settings.update-single');

        // Laporan
        Route::prefix('laporan')->name('laporan.')->group(function () {
            Route::get('/', [SuperAdminLaporan::class, 'index'])->name('index');
            Route::get('/export-excel', [SuperAdminLaporan::class, 'exportExcel'])->name('export-excel');
            Route::get('/chart-data', [SuperAdminLaporan::class, 'chartData'])->name('chart-data');
            Route::get('/statistik-periode', [SuperAdminLaporan::class, 'statistikPeriode'])->name('statistik-periode');
            Route::get('/dosen/{dosenId}', [SuperAdminLaporan::class, 'detailDosen'])->name('detail-dosen');
            Route::get('/jawaban/{penilaianId}', [SuperAdminLaporan::class, 'getDetailJawaban'])->name('detail-jawaban');
        });

        Route::resource('periode', KuesionerPeriodeController::class);
        Route::post('periode/{periode}/toggle-aktif', [KuesionerPeriodeController::class, 'toggleActive'])->name('periode.toggle-aktif');

        Route::post('notifikasi/{id}/read', [SuperAdminDashboard::class, 'markNotificationRead'])->name('notifikasi.read');
        Route::post('notifikasi/read-all', [SuperAdminDashboard::class, 'markAllNotificationsRead'])->name('notifikasi.read-all');
    });

    // =====================================================================
// 5. ADMIN (role: admin)
// =====================================================================
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        // Dashboard & toggle user
        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
        Route::post('user/{user}/toggle-active', [AdminDashboard::class, 'toggleActive'])->name('user.toggle-active');

        // Chart data untuk dashboard (tambahkan ini)
        Route::get('chart-data', [AdminDashboard::class, 'chartData'])->name('chart-data');

        // Manajemen Dosen
        Route::resource('dosen', DosenController::class);
        Route::post('dosen/{dosen}/toggle-active', [DosenController::class, 'toggleActive'])->name('dosen.toggle-active');
        Route::get('dosen/prodi-by-jurusan/{jurusan_id}', [DosenController::class, 'getProdiByJurusan'])->name('dosen.prodi-by-jurusan');

        // Manajemen Mahasiswa
        Route::resource('mahasiswa', MahasiswaController::class);
        Route::get('mahasiswa/prodi-by-jurusan/{jurusan_id}', [MahasiswaController::class, 'getProdiByJurusan'])->name('mahasiswa.prodi-by-jurusan');
        Route::post('mahasiswa/{mahasiswa}/toggle-active', [MahasiswaController::class, 'toggleActive'])->name('mahasiswa.toggle-active');

        // Manajemen Pertanyaan SERVQUAL
        Route::resource('pertanyaan', PertanyaanController::class);
        Route::post('pertanyaan/{pertanyaan}/toggle-active', [PertanyaanController::class, 'toggleActive'])->name('pertanyaan.toggle-active');

        // ========== MANAJEMEN KUESIONER (GLOBAL & PERIODE) ==========
        Route::get('kuesioner', [KuesionerController::class, 'index'])->name('kuesioner.index');
        Route::post('kuesioner/settings', [KuesionerController::class, 'updateSettings'])->name('kuesioner.settings');
        Route::post('kuesioner/toggle-status', [KuesionerController::class, 'toggleGlobalStatus'])->name('kuesioner.toggle-status');
        Route::get('kuesioner/prodi-by-jurusan/{jurusan_id}', [KuesionerController::class, 'getProdiByJurusan'])->name('kuesioner.prodi-by-jurusan');
        Route::get('kuesioner/prodi/{id}', [KuesionerController::class, 'getProdiDetail'])->name('kuesioner.prodi-detail');
        Route::post('kuesioner/periode', [KuesionerController::class, 'storePeriode'])->name('kuesioner.periode.store');
        Route::put('kuesioner/periode/{id}', [KuesionerController::class, 'updatePeriode'])->name('kuesioner.periode.update');
        Route::delete('kuesioner/periode/{id}', [KuesionerController::class, 'destroyPeriode'])->name('kuesioner.periode.destroy');
        Route::post('kuesioner/periode/{id}/toggle-aktif', [KuesionerController::class, 'togglePeriodeActive'])->name('kuesioner.periode.toggle-aktif');
        Route::post('kuesioner/periode/{id}/set-aktif', [KuesionerController::class, 'setActivePeriode'])->name('kuesioner.periode.set-aktif');

        // Laporan Admin (perbaiki prefix route)
        Route::prefix('laporan')->name('laporan.')->group(function () {
            Route::get('/', [AdminLaporan::class, 'index'])->name('index');
            Route::get('export-excel', [AdminLaporan::class, 'exportExcel'])->name('export-excel');
            Route::get('chart-data', [AdminLaporan::class, 'chartData'])->name('chart-data');
            Route::get('dosen/{dosen}', [AdminLaporan::class, 'detailDosen'])->name('detail-dosen');
            Route::get('jawaban/{penilaianId}', [AdminLaporan::class, 'getDetailJawaban'])->name('detail-jawaban');
        });

        // Notifikasi Admin
        Route::post('notifikasi/{id}/read', [AdminDashboard::class, 'markNotificationRead'])->name('notifikasi.read');
        Route::post('notifikasi/read-all', [AdminDashboard::class, 'markAllNotificationsRead'])->name('notifikasi.read-all');
    });

    // =====================================================================
// 6. DOSEN (role: dosen)
// =====================================================================
    Route::prefix('dosen')->name('dosen.')->middleware(['auth', 'role:dosen'])->group(function () {
        Route::get('/dashboard', [DosenDashboard::class, 'index'])->name('dashboard');
        Route::get('/chart-data', [DosenDashboard::class, 'chartData'])->name('chart-data');
        Route::get('/detail-jawaban/{id}', [DosenDashboard::class, 'getDetailJawaban'])->name('detail-jawaban');

        Route::get('kuesioner-fasilitas', [KuesionerFasilitasController::class, 'create'])->name('kuesioner-fasilitas.create');
        Route::post('kuesioner-fasilitas', [KuesionerFasilitasController::class, 'store'])->name('kuesioner-fasilitas.store');
        Route::get('kuesioner-fasilitas/thankyou', [KuesionerFasilitasController::class, 'thankyou'])->name('kuesioner-fasilitas.thankyou');

        Route::get('penilaian-mahasiswa', [PenilaianMahasiswaController::class, 'index'])->name('penilaian-mahasiswa.index');
        Route::get('penilaian-mahasiswa/{id}/detail', [PenilaianMahasiswaController::class, 'getDetailJawaban'])->name('penilaian-mahasiswa.detail'); // ROUTE UNTUK AJAX
        Route::get('penilaian-mahasiswa/export-excel', [PenilaianMahasiswaController::class, 'exportExcel'])->name('penilaian-mahasiswa.export-excel');

        Route::post('notifikasi/{id}/read', [DosenDashboard::class, 'markNotificationRead'])->name('notifikasi.read');
        Route::post('notifikasi/read-all', [DosenDashboard::class, 'markAllNotificationsRead'])->name('notifikasi.read-all');
    });
});

// =========================================================================
// 7. Auth Routes dari Breeze
// =========================================================================
require __DIR__ . '/auth.php';