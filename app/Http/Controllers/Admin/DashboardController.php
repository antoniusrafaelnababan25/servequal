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
        $penilaianQuery = PenilaianDosen::query();
        if ($periodeTerpilih) {
            $penilaianQuery->where('periode_id', $periodeTerpilih->id);
        }

        // Query untuk penilaian fasilitas dengan filter periode
        $fasilitasQuery = PenilaianFasilitas::query();
        if ($periodeTerpilih) {
            $fasilitasQuery->where('periode_id', $periodeTerpilih->id);
        }

        // Statistik
        $totalDosen = User::where('role', 'dosen')->count();
        $totalMahasiswa = User::where('role', 'mahasiswa')->count();
        $totalPenilaianDosen = $penilaianQuery->count();
        $totalPenilaianFasilitas = $fasilitasQuery->count();
        $avgKepuasan = $penilaianQuery->avg('rata_rata') ?? 0;

        // Chart data berdasarkan periode (pastikan selalu ada data)
        $chartData = $this->getChartData($periodeTerpilih ? $periodeTerpilih->id : null);

        // Notifikasi untuk admin
        $notifikasi = Notifikasi::where(function ($q) {
            $q->where('target_role', 'admin')->orWhere('target_role', 'all');
        })->orderBy('created_at', 'desc')->get();
        $unreadCount = $notifikasi->where('is_read', false)->count();

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
            'avgKepuasan',
            'chartData',
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
        $query = PenilaianDosen::query();
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

            foreach ($nilai as $key => $item) {
                // Cari dimensi dari id_pertanyaan
                $idPertanyaan = is_array($item) ? ($item['id_pertanyaan'] ?? $key) : $key;
                $pertanyaan = Pertanyaan::find($idPertanyaan);
                $dimensiItem = $pertanyaan ? $pertanyaan->dimensi : null;

                if ($dimensiItem && in_array($dimensiItem, $dimensi)) {
                    if (is_array($item)) {
                        $totalPersepsi[$dimensiItem] += (int) ($item['persepsi'] ?? 0);
                        $totalHarapan[$dimensiItem] += (int) ($item['harapan'] ?? 0);
                    } else {
                        $totalPersepsi[$dimensiItem] += (int) ($item ?? 0);
                        $totalHarapan[$dimensiItem] += (int) ($item ?? 0);
                    }
                    $count[$dimensiItem]++;
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
     * Toggle status aktif/nonaktif user
     */
    public function toggleActive(User $user)
    {
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
    }

    // Notifikasi
    public function markNotificationRead($id)
    {
        $notif = Notifikasi::findOrFail($id);
        $notif->is_read = true;
        $notif->save();
        return response()->json(['success' => true]);
    }

    public function markAllNotificationsRead()
    {
        Notifikasi::where('target_role', 'admin')->orWhere('target_role', 'all')->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }

    public function deleteNotification($id)
    {
        Notifikasi::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}