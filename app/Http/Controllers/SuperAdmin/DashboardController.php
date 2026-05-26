<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PenilaianDosen;
use App\Models\PenilaianFasilitas;
use App\Models\Notifikasi;
use App\Models\Jurusan;
use App\Models\Prodi;
use App\Models\KuesionerPeriode;
use App\Models\Pertanyaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Dashboard Super Admin dengan statistik, chart, dan daftar user
     */
    public function index(Request $request)
    {
        // Ambil periode yang dipilih dari request
        $periodeId = $request->input('periode_id');
        $periodeTerpilih = null;

        if ($periodeId) {
            $periodeTerpilih = KuesionerPeriode::find($periodeId);
        }

        // Daftar semua periode untuk dropdown - urutkan berdasarkan tanggal
        $periodeList = KuesionerPeriode::orderBy('tanggal_mulai', 'desc')->get();

        // ==================== STATISTIK USER ====================
        $userStats = [
            'super_admin' => User::where('role', 'super_admin')->count(),
            'admin' => User::where('role', 'admin')->count(),
            'dosen' => User::where('role', 'dosen')->count(),
            'mahasiswa' => User::where('role', 'mahasiswa')->count(),
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
        ];

        // ==================== STATISTIK MASTER DATA ====================
        $masterStats = [
            'jurusan' => Jurusan::count(),
            'prodi' => Prodi::count(),
            'periode' => KuesionerPeriode::count(),
            'pertanyaan' => Pertanyaan::count(),
            'pertanyaan_aktif' => Pertanyaan::where('is_active', true)->count(),
        ];

        // ==================== STATISTIK PENILAIAN ====================
        $penilaianQuery = PenilaianDosen::query();
        $fasilitasQuery = PenilaianFasilitas::query();

        if ($periodeTerpilih) {
            $penilaianQuery->where('periode_id', $periodeTerpilih->id);
            $fasilitasQuery->where('periode_id', $periodeTerpilih->id);
        }

        $totalPenilaianDosen = $penilaianQuery->count();
        $totalPenilaianFasilitas = $fasilitasQuery->count();
        $avgKepuasanDosen = round($penilaianQuery->avg('rata_rata') ?? 0, 2);
        $avgKepuasanFasilitas = round($fasilitasQuery->avg('rata_rata') ?? 0, 2);

        // ==================== PARTISIPASI ====================
        $totalMahasiswa = User::where('role', 'mahasiswa')->count();
        $mahasiswaSudahMenilai = PenilaianDosen::when($periodeTerpilih, function ($q) use ($periodeTerpilih) {
            return $q->where('periode_id', $periodeTerpilih->id);
        })->distinct('mahasiswa_id')->count('mahasiswa_id');

        $totalDosen = User::where('role', 'dosen')->count();
        $dosenSudahMenilaiFasilitas = PenilaianFasilitas::when($periodeTerpilih, function ($q) use ($periodeTerpilih) {
            return $q->where('periode_id', $periodeTerpilih->id);
        })->distinct('mahasiswa_id')->count('mahasiswa_id');

        $partisipasi = [
            'mahasiswa' => [
                'sudah' => $mahasiswaSudahMenilai,
                'belum' => $totalMahasiswa - $mahasiswaSudahMenilai,
                'persentase' => $totalMahasiswa > 0 ? round(($mahasiswaSudahMenilai / $totalMahasiswa) * 100, 1) : 0,
            ],
            'dosen' => [
                'sudah' => $dosenSudahMenilaiFasilitas,
                'belum' => $totalDosen - $dosenSudahMenilaiFasilitas,
                'persentase' => $totalDosen > 0 ? round(($dosenSudahMenilaiFasilitas / $totalDosen) * 100, 1) : 0,
            ],
        ];

        // ==================== DATA CHART ====================
        $chartData = $this->getChartData($periodeTerpilih ? $periodeTerpilih->id : null);
        $trendData = $this->getTrendData();
        $kategoriChartData = $this->getKategoriChartData($periodeTerpilih ? $periodeTerpilih->id : null);

        // ==================== NOTIFIKASI ====================
        $notifikasi = Notifikasi::where(function ($q) {
            $q->where('target_role', 'super_admin')->orWhere('target_role', 'all');
        })->orderBy('created_at', 'desc')->limit(10)->get();
        $unreadCount = Notifikasi::where(function ($q) {
            $q->where('target_role', 'super_admin')->orWhere('target_role', 'all');
        })->where('is_read', false)->count();

        // ==================== DATA UNTUK FILTER ====================
        $jurusanList = Jurusan::orderBy('nama_jurusan')->get();
        $prodiList = Prodi::with('jurusan')->orderBy('nama_prodi')->get();
        $jenjangList = ['sarjana' => 'Sarjana', 'pascasarjana' => 'Pascasarjana', 'internasional' => 'Internasional'];

        // ==================== AKTIVITAS TERBARU ====================
        $aktivitasTerbaru = PenilaianDosen::with(['dosen', 'mahasiswa', 'periode'])
            ->when($periodeTerpilih, function ($q) use ($periodeTerpilih) {
                return $q->where('periode_id', $periodeTerpilih->id);
            })
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // ==================== RATING DISTRIBUTION ====================
        $ratingDistribution = $this->getRatingDistribution($periodeTerpilih ? $periodeTerpilih->id : null);

        // ==================== PENILAIAN PER HARI (30 Hari Terakhir) ====================
        $penilaianPerHari = $this->getPenilaianPerHari($periodeTerpilih ? $periodeTerpilih->id : null);

        // User dengan filter (semua role)
        $users = $this->getFilteredUsers($request);

        return view('superadmin.dashboard', compact(
            'userStats',
            'masterStats',
            'totalPenilaianDosen',
            'totalPenilaianFasilitas',
            'avgKepuasanDosen',
            'avgKepuasanFasilitas',
            'chartData',
            'trendData',
            'kategoriChartData',
            'notifikasi',
            'unreadCount',
            'jurusanList',
            'prodiList',
            'jenjangList',
            'periodeList',
            'periodeTerpilih',
            'partisipasi',
            'aktivitasTerbaru',
            'ratingDistribution',
            'penilaianPerHari',
            'users'
        ));
    }

    /**
     * API: Data chart gap per dimensi (dengan filter periode)
     */
    public function chartData(Request $request)
    {
        $periodeId = $request->input('periode_id');
        return response()->json($this->getChartData($periodeId));
    }

    /**
     * Hitung rata-rata persepsi dan harapan untuk setiap dimensi SERVQUAL
     */
    private function getChartData($periodeId = null)
    {
        $defaultData = [
            'Tangible' => ['persepsi' => 0, 'harapan' => 0, 'gap' => 0],
            'Reliability' => ['persepsi' => 0, 'harapan' => 0, 'gap' => 0],
            'Responsiveness' => ['persepsi' => 0, 'harapan' => 0, 'gap' => 0],
            'Assurance' => ['persepsi' => 0, 'harapan' => 0, 'gap' => 0],
            'Empathy' => ['persepsi' => 0, 'harapan' => 0, 'gap' => 0],
        ];

        $dimensi = ['Tangible', 'Reliability', 'Responsiveness', 'Assurance', 'Empathy'];

        $query = PenilaianDosen::query();
        if ($periodeId) {
            $query->where('periode_id', $periodeId);
        }

        $penilaianList = $query->get();

        if ($penilaianList->isEmpty()) {
            return $defaultData;
        }

        $totalPersepsi = array_fill_keys($dimensi, 0);
        $totalHarapan = array_fill_keys($dimensi, 0);
        $count = array_fill_keys($dimensi, 0);

        foreach ($penilaianList as $penilaian) {
            $nilai = $penilaian->nilai;
            if (is_string($nilai)) {
                $nilai = json_decode($nilai, true);
            }

            if (!is_array($nilai)) {
                continue;
            }

            foreach ($nilai as $item) {
                if (is_array($item) && isset($item['id_pertanyaan'])) {
                    $pertanyaan = Pertanyaan::find($item['id_pertanyaan']);
                    $dimensiItem = $pertanyaan ? $pertanyaan->dimensi : null;

                    if ($dimensiItem && in_array($dimensiItem, $dimensi)) {
                        $totalPersepsi[$dimensiItem] += (int) ($item['persepsi'] ?? 0);
                        $totalHarapan[$dimensiItem] += (int) ($item['harapan'] ?? 0);
                        $count[$dimensiItem]++;
                    }
                }
            }
        }

        $result = [];
        foreach ($dimensi as $dim) {
            $avgPersepsi = $count[$dim] > 0 ? round($totalPersepsi[$dim] / $count[$dim], 2) : 0;
            $avgHarapan = $count[$dim] > 0 ? round($totalHarapan[$dim] / $count[$dim], 2) : 0;
            $result[$dim] = [
                'persepsi' => $avgPersepsi,
                'harapan' => $avgHarapan,
                'gap' => round($avgPersepsi - $avgHarapan, 2),
            ];
        }

        return $result;
    }

    /**
     * Chart data untuk penilaian fasilitas per kategori
     */
    private function getKategoriChartData($periodeId = null)
    {
        $kategori = ['umum', 'peralatan', 'ruangan', 'akses', 'infrastruktur'];
        $kategoriLabel = [
            'umum' => 'Umum',
            'peralatan' => 'Peralatan',
            'ruangan' => 'Ruangan',
            'akses' => 'Akses',
            'infrastruktur' => 'Infrastruktur'
        ];

        $defaultData = [];
        foreach ($kategori as $kat) {
            $defaultData[$kat] = ['persepsi' => 0, 'harapan' => 0, 'gap' => 0, 'label' => $kategoriLabel[$kat]];
        }

        $query = PenilaianFasilitas::query();
        if ($periodeId) {
            $query->where('periode_id', $periodeId);
        }

        $penilaianList = $query->get();

        if ($penilaianList->isEmpty()) {
            return $defaultData;
        }

        $totalPersepsi = array_fill_keys($kategori, 0);
        $totalHarapan = array_fill_keys($kategori, 0);
        $count = array_fill_keys($kategori, 0);

        foreach ($penilaianList as $penilaian) {
            $nilai = $penilaian->nilai;
            if (is_string($nilai)) {
                $nilai = json_decode($nilai, true);
            }
            if (is_array($nilai)) {
                foreach ($nilai as $item) {
                    if (isset($item['id_pertanyaan'])) {
                        $pertanyaan = Pertanyaan::find($item['id_pertanyaan']);
                        $kategoriItem = $pertanyaan ? $pertanyaan->kategori_fasilitas : null;
                        if ($kategoriItem && in_array($kategoriItem, $kategori)) {
                            $totalPersepsi[$kategoriItem] += $item['persepsi'] ?? 0;
                            $totalHarapan[$kategoriItem] += $item['harapan'] ?? 0;
                            $count[$kategoriItem]++;
                        }
                    }
                }
            }
        }

        $result = [];
        foreach ($kategori as $kat) {
            $avgPersepsi = $count[$kat] > 0 ? round($totalPersepsi[$kat] / $count[$kat], 2) : 0;
            $avgHarapan = $count[$kat] > 0 ? round($totalHarapan[$kat] / $count[$kat], 2) : 0;
            $result[$kat] = [
                'persepsi' => $avgPersepsi,
                'harapan' => $avgHarapan,
                'gap' => round($avgPersepsi - $avgHarapan, 2),
                'label' => $kategoriLabel[$kat]
            ];
        }

        return $result;
    }

    /**
     * Data tren SERVQUAL antar periode
     */
    private function getTrendData()
    {
        $periodeList = KuesionerPeriode::orderBy('tanggal_mulai', 'asc')->get();
        $trend = [];

        foreach ($periodeList as $periode) {
            $penilaianList = PenilaianDosen::where('periode_id', $periode->id)->get();
            $rataKepuasan = $penilaianList->avg('rata_rata') ?? 0;

            $trend[] = [
                'periode' => [
                    'id' => $periode->id,
                    'nama' => $periode->nama_periode,
                    'tanggal_mulai' => $periode->tanggal_mulai,
                    'tanggal_selesai' => $periode->tanggal_selesai,
                ],
                'rata_kepuasan' => round($rataKepuasan, 2),
                'total_penilaian' => $penilaianList->count(),
            ];
        }

        return $trend;
    }

    /**
     * Distribusi rating (1-5)
     */
    private function getRatingDistribution($periodeId = null)
    {
        $query = PenilaianDosen::query();
        if ($periodeId) {
            $query->where('periode_id', $periodeId);
        }

        $ratings = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        $rataRataList = $query->pluck('rata_rata');

        foreach ($rataRataList as $rata) {
            $rating = (int) round($rata);
            if (isset($ratings[$rating])) {
                $ratings[$rating]++;
            }
        }

        return $ratings;
    }

    /**
     * Penilaian per hari (30 hari terakhir)
     */
    private function getPenilaianPerHari($periodeId = null)
    {
        $query = PenilaianDosen::query();
        if ($periodeId) {
            $query->where('periode_id', $periodeId);
        }

        $data = $query->select(DB::raw('DATE(created_at) as tanggal'), DB::raw('COUNT(*) as total'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get();

        $result = [];
        for ($i = 29; $i >= 0; $i--) {
            $tanggal = now()->subDays($i)->format('Y-m-d');
            $result[$tanggal] = 0;
        }

        foreach ($data as $item) {
            $result[$item->tanggal] = $item->total;
        }

        return $result;
    }

    /**
     * Filter user (semua role) berdasarkan request
     */
    private function getFilteredUsers(Request $request)
    {
        $query = User::with('prodi.jurusan');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('jenjang')) {
            $query->whereHas('prodi', function ($q) use ($request) {
                $q->where('jenjang', $request->jenjang);
            });
        }
        if ($request->filled('jurusan_id')) {
            $query->whereHas('prodi', function ($q) use ($request) {
                $q->where('jurusan_id', $request->jurusan_id);
            });
        }
        if ($request->filled('prodi_id')) {
            $query->where('prodi_id', $request->prodi_id);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('nidn', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate(10);
    }

    /**
     * API: Ambil data user terfilter
     */
    public function getUsersJson(Request $request)
    {
        return response()->json($this->getFilteredUsers($request));
    }

    /**
     * Toggle user active status
     */
    public function toggleActive(User $user)
    {
        try {
            $user->is_active = !$user->is_active;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => "Status user {$user->name} berhasil diubah",
                'is_active' => $user->is_active
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status user'
            ], 500);
        }
    }

    /**
     * Notifikasi: Mark as read
     */
    public function markNotificationRead($id)
    {
        try {
            $notif = Notifikasi::findOrFail($id);
            $notif->is_read = true;
            $notif->save();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Notifikasi: Mark all as read
     */
    public function markAllNotificationsRead()
    {
        try {
            Notifikasi::where('target_role', 'super_admin')->orWhere('target_role', 'all')->update(['is_read' => true]);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Notifikasi: Delete notification
     */
    public function deleteNotification($id)
    {
        try {
            Notifikasi::findOrFail($id)->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }
}