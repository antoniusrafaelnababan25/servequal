<?php

namespace App\Http\Controllers;

use App\Models\KuesionerPeriode;
use App\Models\SystemSetting;
use App\Models\PenilaianDosen;
use App\Models\PenilaianFasilitas;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Tampilkan halaman landing page (welcome)
     */
    public function index()
    {
        // Cek periode aktif
        $periodeAktif = KuesionerPeriode::where('is_active', true)
            ->where('status', 'aktif')
            ->whereDate('tanggal_mulai', '<=', now())
            ->whereDate('tanggal_selesai', '>=', now())
            ->first();

        // Status global kuesioner
        $statusKuesioner = SystemSetting::get('kuesioner_status', 'closed');

        // Statistik umum untuk landing page
        $totalDosen = User::where('role', 'dosen')->count();
        $totalMahasiswa = User::where('role', 'mahasiswa')->count();
        $totalPenilaian = PenilaianDosen::count();
        $rataKepuasan = PenilaianDosen::avg('rata_rata') ?? 0;

        // Data untuk chart di landing (ringkasan per dimensi)
        $chartData = $this->getRingkasanDimensi();

        // Informasi sistem
        $appName = SystemSetting::get('app_name', 'Sistem Monitoring SERVQUAL');
        $appVersion = SystemSetting::get('app_version', '1.0.0');
        $tujuan = SystemSetting::get('tujuan_kuesioner', 'Mengukur kepuasan layanan akademik dengan metode SERVQUAL');

        // Data periode terbaru (untuk ditampilkan)
        $periodeTerbaru = KuesionerPeriode::orderBy('created_at', 'desc')->limit(3)->get();

        return view('welcome', compact(
            'periodeAktif',
            'statusKuesioner',
            'totalDosen',
            'totalMahasiswa',
            'totalPenilaian',
            'rataKepuasan',
            'chartData',
            'appName',
            'appVersion',
            'tujuan',
            'periodeTerbaru'
        ));
    }

    /**
     * Mendapatkan ringkasan data gap per dimensi untuk ditampilkan di landing page
     */
    private function getRingkasanDimensi()
    {
        $dimensi = ['Tangible', 'Reliability', 'Responsiveness', 'Assurance', 'Empathy'];
        $result = [];

        foreach ($dimensi as $dim) {
            $persepsi = PenilaianDosen::whereRaw("JSON_CONTAINS(nilai, JSON_OBJECT('dimensi', ?), '$')", [$dim])
                ->selectRaw("COALESCE(AVG(JSON_EXTRACT(nilai, '$[*].persepsi')), 0) as avg")
                ->value('avg') ?? 0;

            $harapan = PenilaianDosen::whereRaw("JSON_CONTAINS(nilai, JSON_OBJECT('dimensi', ?), '$')", [$dim])
                ->selectRaw("COALESCE(AVG(JSON_EXTRACT(nilai, '$[*].harapan')), 0) as avg")
                ->value('avg') ?? 0;

            $result[$dim] = [
                'persepsi' => round($persepsi, 2),
                'harapan' => round($harapan, 2),
                'gap' => round($persepsi - $harapan, 2),
            ];
        }
        return $result;
    }

    /**
     * Halaman tentang sistem
     */
    public function about()
    {
        $appName = SystemSetting::get('app_name', 'Sistem Monitoring SERVQUAL');
        $appVersion = SystemSetting::get('app_version', '1.0.0');
        return view('about', compact('appName', 'appVersion'));
    }

    /**
     * Halaman bantuan / FAQ
     */
    public function help()
    {
        return view('help');
    }

    /**
     * Halaman kontak
     */
    public function contact()
    {
        return view('contact');
    }
}