<?php

namespace App\Http\Controllers\Admin;

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
     * Tampilkan dashboard admin dengan statistik, chart, dan daftar user
     */
    public function index(Request $request)
    {
        // Ambil periode yang dipilih dari request
        $periodeId = $request->input('periode_id');
        $periodeTerpilih = null;

        if ($periodeId) {
            $periodeTerpilih = KuesionerPeriode::find($periodeId);
        }

        // Daftar semua periode untuk dropdown
        $periodeList = KuesionerPeriode::orderBy('created_at', 'desc')->get();

        // Query dengan filter periode untuk penilaian dosen
        $penilaianDosenQuery = PenilaianDosen::query();
        if ($periodeTerpilih) {
            $penilaianDosenQuery->where('periode_id', $periodeTerpilih->id);
        }

        // Query untuk penilaian fasilitas dengan filter periode
        $penilaianFasilitasQuery = PenilaianFasilitas::query();
        if ($periodeTerpilih) {
            $penilaianFasilitasQuery->where('periode_id', $periodeTerpilih->id);
        }

        // Statistik
        $totalDosen = User::where('role', 'dosen')->count();
        $totalMahasiswa = User::where('role', 'mahasiswa')->count();
        $totalPenilaianDosen = $penilaianDosenQuery->count();
        $totalPenilaianFasilitas = $penilaianFasilitasQuery->count();
        $avgKepuasanDosen = $penilaianDosenQuery->avg('rata_rata') ?? 0;
        $avgKepuasanFasilitas = $penilaianFasilitasQuery->avg('rata_rata') ?? 0;

        // Chart data penilaian dosen
        $chartDataDosen = $this->getChartDataPenilaianDosen($periodeTerpilih ? $periodeTerpilih->id : null);

        // Chart data penilaian fasilitas
        $chartDataFasilitas = $this->getChartDataPenilaianFasilitas($periodeTerpilih ? $periodeTerpilih->id : null);

        // Notifikasi untuk admin
        $notifikasi = Notifikasi::where(function ($q) {
            $q->where('target_role', 'admin')->orWhere('target_role', 'all');
        })->orderBy('created_at', 'desc')->limit(10)->get();
        $unreadCount = Notifikasi::where(function ($q) {
            $q->where('target_role', 'admin')->orWhere('target_role', 'all');
        })->where('is_read', false)->count();

        // User dengan filter (dosen & mahasiswa)
        $users = $this->getFilteredUsers($request);

        // Data untuk dropdown filter
        $jurusanList = Jurusan::orderBy('nama_jurusan')->get();
        $prodiList = Prodi::with('jurusan')->orderBy('nama_prodi')->get();
        $jenjangList = ['sarjana' => 'Sarjana', 'pascasarjana' => 'Pascasarjana', 'internasional' => 'Internasional'];

        return view('admin.dashboard', compact(
            'totalDosen',
            'totalMahasiswa',
            'totalPenilaianDosen',
            'totalPenilaianFasilitas',
            'avgKepuasanDosen',
            'avgKepuasanFasilitas',
            'chartDataDosen',
            'chartDataFasilitas',
            'notifikasi',
            'unreadCount',
            'users',
            'jurusanList',
            'prodiList',
            'jenjangList',
            'periodeList',
            'periodeTerpilih'
        ));
    }

    /**
     * Hitung rata-rata persepsi dan harapan untuk setiap dimensi SERVQUAL (Penilaian Dosen)
     */
    private function getChartDataPenilaianDosen($periodeId = null)
    {
        $dimensi = ['Tangible', 'Reliability', 'Responsiveness', 'Assurance', 'Empathy'];

        // Data default
        $defaultData = [];
        foreach ($dimensi as $dim) {
            $defaultData[$dim] = ['persepsi' => 0, 'harapan' => 0, 'gap' => 0];
        }

        // Ambil semua penilaian sesuai periode
        $query = PenilaianDosen::query();
        if ($periodeId) {
            $query->where('periode_id', $periodeId);
        }

        $penilaianList = $query->get();

        if ($penilaianList->isEmpty()) {
            return $defaultData;
        }

        // Inisialisasi akumulator
        $totalPersepsi = array_fill_keys($dimensi, 0);
        $totalHarapan = array_fill_keys($dimensi, 0);
        $count = array_fill_keys($dimensi, 0);

        foreach ($penilaianList as $penilaian) {
            $nilai = $penilaian->nilai;
            if (is_string($nilai)) {
                $nilai = json_decode($nilai, true);
            }
            if (!is_array($nilai))
                continue;

            foreach ($nilai as $jawaban) {
                if (isset($jawaban['id_pertanyaan'])) {
                    $pertanyaan = Pertanyaan::find($jawaban['id_pertanyaan']);
                    $dimensiItem = $pertanyaan ? $pertanyaan->dimensi : null;

                    if ($dimensiItem && in_array($dimensiItem, $dimensi)) {
                        $totalPersepsi[$dimensiItem] += (int) ($jawaban['persepsi'] ?? 0);
                        $totalHarapan[$dimensiItem] += (int) ($jawaban['harapan'] ?? 0);
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
     * Hitung rata-rata persepsi dan harapan untuk setiap kategori (Penilaian Fasilitas)
     */
    private function getChartDataPenilaianFasilitas($periodeId = null)
    {
        $kategori = ['umum', 'peralatan', 'ruangan', 'akses', 'infrastruktur'];
        $kategoriLabel = [
            'umum' => 'Umum',
            'peralatan' => 'Peralatan',
            'ruangan' => 'Ruangan',
            'akses' => 'Akses',
            'infrastruktur' => 'Infrastruktur'
        ];

        // Data default
        $defaultData = [];
        foreach ($kategori as $kat) {
            $defaultData[$kat] = ['persepsi' => 0, 'harapan' => 0, 'gap' => 0, 'label' => $kategoriLabel[$kat]];
        }

        // Ambil semua penilaian fasilitas sesuai periode
        $query = PenilaianFasilitas::query();
        if ($periodeId) {
            $query->where('periode_id', $periodeId);
        }

        $penilaianList = $query->get();

        if ($penilaianList->isEmpty()) {
            return $defaultData;
        }

        // Inisialisasi akumulator
        $totalPersepsi = array_fill_keys($kategori, 0);
        $totalHarapan = array_fill_keys($kategori, 0);
        $count = array_fill_keys($kategori, 0);

        foreach ($penilaianList as $penilaian) {
            $nilai = $penilaian->nilai;
            if (is_string($nilai)) {
                $nilai = json_decode($nilai, true);
            }
            if (!is_array($nilai))
                continue;

            foreach ($nilai as $jawaban) {
                if (isset($jawaban['id_pertanyaan'])) {
                    $pertanyaan = Pertanyaan::find($jawaban['id_pertanyaan']);
                    $kategoriItem = $pertanyaan ? $pertanyaan->kategori_fasilitas : null;

                    if ($kategoriItem && in_array($kategoriItem, $kategori)) {
                        $totalPersepsi[$kategoriItem] += (int) ($jawaban['persepsi'] ?? 0);
                        $totalHarapan[$kategoriItem] += (int) ($jawaban['harapan'] ?? 0);
                        $count[$kategoriItem]++;
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
     * API: Data chart gap per dimensi (Penilaian Dosen)
     */
    public function chartDataDosen(Request $request)
    {
        $periodeId = $request->input('periode_id');
        return response()->json($this->getChartDataPenilaianDosen($periodeId));
    }

    /**
     * API: Data chart gap per kategori (Penilaian Fasilitas)
     */
    public function chartDataFasilitas(Request $request)
    {
        $periodeId = $request->input('periode_id');
        return response()->json($this->getChartDataPenilaianFasilitas($periodeId));
    }

    /**
     * Filter user (dosen & mahasiswa) berdasarkan request
     */
    private function getFilteredUsers(Request $request)
    {
        $query = User::whereIn('role', ['dosen', 'mahasiswa'])->with('prodi.jurusan');

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
     * Toggle status aktif/nonaktif user
     */
    public function toggleActive(User $user)
    {
        try {
            if (!in_array($user->role, ['dosen', 'mahasiswa'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak diizinkan'
                ], 403);
            }

            $user->is_active = !$user->is_active;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Status user berhasil diubah',
                'is_active' => $user->is_active
            ]);
        } catch (\Exception $e) {
            Log::error('Error toggling user status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status user'
            ], 500);
        }
    }

    /**
     * Mark notification as read
     */
    public function markNotificationRead($id)
    {
        try {
            $notif = Notifikasi::findOrFail($id);
            $notif->is_read = true;
            $notif->save();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsRead()
    {
        try {
            Notifikasi::where('target_role', 'admin')->orWhere('target_role', 'all')->update(['is_read' => true]);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete notification
     */
    public function deleteNotification($id)
    {
        try {
            Notifikasi::findOrFail($id)->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}