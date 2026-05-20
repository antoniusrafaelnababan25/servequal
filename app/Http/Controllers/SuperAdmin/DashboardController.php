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

        // ==================== PARTISIPASI MAHASISWA ====================
        $totalMahasiswa = User::where('role', 'mahasiswa')->count();
        $mahasiswaSudahMenilai = PenilaianDosen::when($periodeTerpilih, function ($q) use ($periodeTerpilih) {
            return $q->where('periode_id', $periodeTerpilih->id);
        })->distinct('mahasiswa_id')->count('mahasiswa_id');

        $partisipasi = [
            'sudah_menilai' => $mahasiswaSudahMenilai,
            'belum_menilai' => $totalMahasiswa - $mahasiswaSudahMenilai,
            'persentase' => $totalMahasiswa > 0 ? round(($mahasiswaSudahMenilai / $totalMahasiswa) * 100, 1) : 0,
        ];

        // ==================== DATA CHART ====================
        // Chart data berdasarkan periode (pastikan selalu ada data)
        $chartData = $this->getChartData($periodeTerpilih ? $periodeTerpilih->id : null);
        $trendData = $this->getTrendData();

        // ==================== NOTIFIKASI ====================
        $notifikasi = Notifikasi::where(function ($q) {
            $q->where('target_role', 'super_admin')->orWhere('target_role', 'all');
        })->orderBy('created_at', 'desc')->get();
        $unreadCount = $notifikasi->where('is_read', false)->count();

        // ==================== DATA UNTUK FILTER ====================
        $jurusanList = Jurusan::orderBy('nama_jurusan')->get();
        $prodiList = Prodi::with('jurusan')->orderBy('nama_prodi')->get();
        $jenjangList = ['sarjana' => 'Sarjana', 'pascasarjana' => 'Pascasarjana', 'internasional' => 'Internasional'];

        // ==================== AKTIVITAS TERBARU ====================
        $aktivitasTerbaru = $this->getAktivitasTerbaru($periodeTerpilih?->id);

        // ==================== RATING DISTRIBUTION ====================
        $ratingDistribution = $this->getRatingDistribution($periodeTerpilih?->id);

        // User dengan filter (semua role)
        $users = $this->getFilteredUsers($request);

        return view('superadmin.dashboard', compact(
            'userStats',
            'totalPenilaianDosen',
            'totalPenilaianFasilitas',
            'avgKepuasanDosen',
            'avgKepuasanFasilitas',
            'chartData',
            'trendData',
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
     * Pastikan selalu mengembalikan data, tidak pernah null atau kosong
     */
    private function getChartData($periodeId = null)
    {
        // Data dummy default (akan digunakan jika tidak ada data penilaian)
        $defaultData = [
            'Tangible' => ['persepsi' => 0, 'harapan' => 0, 'gap' => 0],
            'Reliability' => ['persepsi' => 0, 'harapan' => 0, 'gap' => 0],
            'Responsiveness' => ['persepsi' => 0, 'harapan' => 0, 'gap' => 0],
            'Assurance' => ['persepsi' => 0, 'harapan' => 0, 'gap' => 0],
            'Empathy' => ['persepsi' => 0, 'harapan' => 0, 'gap' => 0],
        ];

        $dimensi = ['Tangible', 'Reliability', 'Responsiveness', 'Assurance', 'Empathy'];

        // Ambil semua penilaian sesuai periode
        $query = PenilaianDosen::with(['dosen']);
        if ($periodeId) {
            $query->where('periode_id', $periodeId);
        }

        $penilaianList = $query->get();

        // Jika tidak ada data, return default (semua 0)
        if ($penilaianList->isEmpty()) {
            return $defaultData;
        }

        // Inisialisasi akumulator
        $totalPersepsi = array_fill_keys($dimensi, 0);
        $totalHarapan = array_fill_keys($dimensi, 0);
        $count = array_fill_keys($dimensi, 0);

        foreach ($penilaianList as $penilaian) {
            $nilai = $penilaian->nilai;

            // Decode jika string JSON
            if (is_string($nilai)) {
                $nilai = json_decode($nilai, true);
            }

            if (!is_array($nilai)) {
                continue;
            }

            foreach ($nilai as $item) {
                // Cari dimensi dari id_pertanyaan
                $idPertanyaan = null;
                if (is_array($item)) {
                    $idPertanyaan = $item['id_pertanyaan'] ?? null;
                } elseif (is_object($item)) {
                    $idPertanyaan = $item->id_pertanyaan ?? null;
                }

                if ($idPertanyaan) {
                    $pertanyaan = Pertanyaan::find($idPertanyaan);
                    $dimensiItem = $pertanyaan ? $pertanyaan->dimensi : null;

                    if ($dimensiItem && in_array($dimensiItem, $dimensi)) {
                        if (is_array($item)) {
                            $totalPersepsi[$dimensiItem] += (int) ($item['persepsi'] ?? 0);
                            $totalHarapan[$dimensiItem] += (int) ($item['harapan'] ?? 0);
                        } else {
                            $totalPersepsi[$dimensiItem] += (int) ($item->persepsi ?? 0);
                            $totalHarapan[$dimensiItem] += (int) ($item->harapan ?? 0);
                        }
                        $count[$dimensiItem]++;
                    }
                }
            }
        }

        // Hitung rata-rata
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
     * Data tren SERVQUAL antar periode
     */
    private function getTrendData()
    {
        $periodeList = KuesionerPeriode::orderBy('tanggal_mulai', 'asc')->get();

        $trend = [];
        foreach ($periodeList as $periode) {
            $penilaianList = PenilaianDosen::where('periode_id', $periode->id)->get();
            $rataKepuasan = $penilaianList->avg('rata_rata') ?? 0;
            $totalPenilaian = $penilaianList->count();

            // Hitung rata-rata gap
            $totalGap = 0;
            $gapCount = 0;
            foreach ($penilaianList as $penilaian) {
                $nilai = $penilaian->nilai;
                if (is_string($nilai)) {
                    $nilai = json_decode($nilai, true);
                }
                if (is_array($nilai)) {
                    foreach ($nilai as $item) {
                        if (is_array($item) && isset($item['persepsi']) && isset($item['harapan'])) {
                            $gap = ($item['persepsi'] ?? 0) - ($item['harapan'] ?? 0);
                            $totalGap += $gap;
                            $gapCount++;
                        }
                    }
                }
            }

            $trend[] = [
                'periode' => [
                    'id' => $periode->id,
                    'nama' => $periode->nama_periode,
                    'tanggal_mulai' => $periode->tanggal_mulai,
                    'tanggal_selesai' => $periode->tanggal_selesai,
                ],
                'rata_kepuasan' => round($rataKepuasan, 2),
                'total_penilaian' => $totalPenilaian,
                'rata_gap' => $gapCount > 0 ? round($totalGap / $gapCount, 2) : 0,
            ];
        }

        return $trend;
    }

    /**
     * Aktivitas terbaru (penilaian terbaru)
     */
    private function getAktivitasTerbaru($periodeId = null)
    {
        $query = PenilaianDosen::with(['dosen', 'mahasiswa', 'periode'])
            ->orderBy('created_at', 'desc');

        if ($periodeId) {
            $query->where('periode_id', $periodeId);
        }

        return $query->limit(10)->get();
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

        return $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
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
        $user->is_active = !$user->is_active;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => "Status user {$user->name} berhasil diubah",
            'is_active' => $user->is_active
        ]);
    }

    /**
     * Notifikasi: Mark as read
     */
    public function markNotificationRead(int $id)
    {
        $notif = Notifikasi::findOrFail($id);
        $notif->is_read = true;
        $notif->save();
        return response()->json(['success' => true]);
    }

    /**
     * Notifikasi: Mark all as read
     */
    public function markAllNotificationsRead()
    {
        Notifikasi::where('target_role', 'super_admin')->orWhere('target_role', 'all')->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }

    /**
     * Notifikasi: Delete notification
     */
    public function deleteNotification(int $id)
    {
        $notif = Notifikasi::findOrFail($id);
        $notif->delete();
        return response()->json(['success' => true]);
    }
}