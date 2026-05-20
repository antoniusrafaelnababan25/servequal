<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\PenilaianDosen;
use App\Models\KuesionerResponse;
use App\Models\Notifikasi;
use App\Models\Pertanyaan;
use App\Models\KuesionerPeriode;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard dosen
     */
    public function index(Request $request)
    {
        $dosen = Auth::user();

        if (!$dosen) {
            return redirect()->route('login');
        }

        if ($dosen->role !== 'dosen') {
            abort(403, 'Anda bukan dosen');
        }

        // Ambil periode yang dipilih
        $periodeId = $request->input('periode_id');
        $periodeTerpilih = null;

        if ($periodeId) {
            $periodeTerpilih = KuesionerPeriode::find($periodeId);
        }

        // Daftar periode untuk dropdown
        $periodeList = KuesionerPeriode::orderBy('created_at', 'desc')->get();

        // Query penilaian dengan filter periode
        $penilaianQuery = PenilaianDosen::where('dosen_id', $dosen->id);
        if ($periodeTerpilih) {
            $penilaianQuery->where('periode_id', $periodeTerpilih->id);
        }

        // Statistik
        $totalPenilaian = $penilaianQuery->count();
        $rataRataKeseluruhan = $penilaianQuery->avg('rata_rata') ?? 0;
        $nilaiTertinggi = $penilaianQuery->max('rata_rata') ?? 0;
        $nilaiTerendah = $penilaianQuery->min('rata_rata') ?? 0;

        // Rata-rata per dimensi
        $chartData = $this->getGapPerDimensi($dosen->id, $periodeTerpilih);
        $chartJsData = $this->formatChartData($chartData);

        // Apakah dosen sudah mengisi kuisioner fasilitas?
        $sudahIsiFasilitas = KuesionerResponse::where('responden_id', $dosen->id)
            ->where('role', 'dosen')
            ->exists();

        // Notifikasi
        $notifikasi = Notifikasi::where(function ($q) use ($dosen) {
            $q->where('target_role', 'dosen')
                ->orWhere('target_role', 'all')
                ->orWhere('target_user_id', $dosen->id);
        })->orderBy('created_at', 'desc')->get();
        $unreadCount = $notifikasi->where('is_read', false)->count();

        // Penilaian terbaru
        $penilaianTerbaru = $penilaianQuery->with('mahasiswa', 'periode')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dosen.dashboard', compact(
            'dosen',
            'totalPenilaian',
            'rataRataKeseluruhan',
            'nilaiTertinggi',
            'nilaiTerendah',
            'chartData',
            'chartJsData',
            'sudahIsiFasilitas',
            'notifikasi',
            'unreadCount',
            'penilaianTerbaru',
            'periodeList',
            'periodeTerpilih'
        ));
    }

    /**
     * API: data chart gap per dimensi
     */
    public function chartData(Request $request): JsonResponse
    {
        $dosenId = Auth::id();
        $periodeId = $request->input('periode_id');
        $periodeTerpilih = $periodeId ? KuesionerPeriode::find($periodeId) : null;

        $data = $this->getGapPerDimensi($dosenId, $periodeTerpilih);

        return response()->json([
            'success' => true,
            'data' => $data,
            'chart' => $this->formatChartData($data)
        ]);
    }

    /**
     * Format chart data untuk Chart.js
     */
    private function formatChartData(array $data): array
    {
        $dimensi = ['Tangible', 'Reliability', 'Responsiveness', 'Assurance', 'Empathy'];
        $persepsi = [];
        $harapan = [];
        $gap = [];

        foreach ($dimensi as $dim) {
            $persepsi[] = $data[$dim]['persepsi'] ?? 0;
            $harapan[] = $data[$dim]['harapan'] ?? 0;
            $gap[] = $data[$dim]['gap'] ?? 0;
        }

        return [
            'labels' => $dimensi,
            'persepsi' => $persepsi,
            'harapan' => $harapan,
            'gap' => $gap
        ];
    }

    /**
     * Hitung rata-rata gap per dimensi
     */
    private function getGapPerDimensi(int $dosenId, $periodeTerpilih = null): array
    {
        $dimensi = ['Tangible', 'Reliability', 'Responsiveness', 'Assurance', 'Empathy'];
        $result = [];

        foreach ($dimensi as $dim) {
            $result[$dim] = ['persepsi' => 0, 'harapan' => 0, 'gap' => 0];
        }

        $query = PenilaianDosen::where('dosen_id', $dosenId);
        if ($periodeTerpilih) {
            $query->where('periode_id', $periodeTerpilih->id);
        }

        $penilaianList = $query->get();

        if ($penilaianList->isEmpty()) {
            return $result;
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

            foreach ($nilai as $key => $item) {
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

        foreach ($dimensi as $dim) {
            if ($count[$dim] > 0) {
                $avgPersepsi = round($totalPersepsi[$dim] / $count[$dim], 2);
                $avgHarapan = round($totalHarapan[$dim] / $count[$dim], 2);
                $result[$dim] = [
                    'persepsi' => $avgPersepsi,
                    'harapan' => $avgHarapan,
                    'gap' => round($avgPersepsi - $avgHarapan, 2),
                ];
            }
        }

        return $result;
    }

    /**
     * API: Detail jawaban per penilaian
     */
    public function getDetailJawaban(int $penilaianId): JsonResponse
    {
        $penilaian = PenilaianDosen::with(['dosen', 'mahasiswa'])->findOrFail($penilaianId);

        // Cek apakah penilaian milik dosen yang login
        if ($penilaian->dosen_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $nilai = $penilaian->nilai;
        if (is_string($nilai)) {
            $nilai = json_decode($nilai, true);
        }

        $detailJawaban = [];

        if (is_array($nilai)) {
            // Urutkan berdasarkan key
            if (isset($nilai[0]) && is_array($nilai[0])) {
                // Format array biasa
                foreach ($nilai as $index => $item) {
                    $idPertanyaan = $item['id_pertanyaan'] ?? ($index + 1);
                    $pertanyaan = Pertanyaan::find($idPertanyaan);

                    $detailJawaban[] = [
                        'no' => $index + 1,
                        'pertanyaan' => $pertanyaan ? $pertanyaan->teks : ($item['teks'] ?? 'Pertanyaan ' . ($index + 1)),
                        'dimensi' => $pertanyaan ? $pertanyaan->dimensi : ($item['dimensi'] ?? '-'),
                        'harapan' => intval($item['harapan'] ?? 0),
                        'persepsi' => intval($item['persepsi'] ?? 0),
                        'gap' => intval($item['persepsi'] ?? 0) - intval($item['harapan'] ?? 0),
                    ];
                }
            } else {
                // Format dengan key id_pertanyaan
                $keys = array_keys($nilai);
                sort($keys);

                foreach ($keys as $index => $key) {
                    $item = $nilai[$key];
                    $idPertanyaan = is_array($item) ? ($item['id_pertanyaan'] ?? $key) : $key;
                    $pertanyaan = Pertanyaan::find($idPertanyaan);

                    $detailJawaban[] = [
                        'no' => $index + 1,
                        'pertanyaan' => $pertanyaan ? $pertanyaan->teks : (is_array($item) ? ($item['teks'] ?? 'Pertanyaan ' . $key) : 'Pertanyaan ' . $key),
                        'dimensi' => $pertanyaan ? $pertanyaan->dimensi : (is_array($item) ? ($item['dimensi'] ?? '-') : '-'),
                        'harapan' => is_array($item) ? intval($item['harapan'] ?? 0) : 0,
                        'persepsi' => is_array($item) ? intval($item['persepsi'] ?? 0) : 0,
                        'gap' => (is_array($item) ? intval($item['persepsi'] ?? 0) : 0) - (is_array($item) ? intval($item['harapan'] ?? 0) : 0),
                    ];
                }
            }
        }

        // Kelompokkan berdasarkan dimensi
        $dimensiJawaban = [];
        foreach ($detailJawaban as $item) {
            if (!isset($dimensiJawaban[$item['dimensi']])) {
                $dimensiJawaban[$item['dimensi']] = [];
            }
            $dimensiJawaban[$item['dimensi']][] = $item;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'dosen' => $penilaian->dosen->name ?? '-',
                'nidn' => $penilaian->dosen->nidn ?? '-',
                'mahasiswa' => $penilaian->mahasiswa->name ?? '-',
                'nim' => $penilaian->mahasiswa->nim ?? '-',
                'kelas' => $penilaian->kelas ?? '-',
                'mata_kuliah' => $penilaian->mata_kuliah ?? '-',
                'periode' => $penilaian->periode ? $penilaian->periode->nama_periode : '-',
                'rata_rata' => $penilaian->rata_rata,
                'tanggal' => $penilaian->created_at->format('d/m/Y H:i'),
                'jawaban' => $detailJawaban,
                'dimensi_jawaban' => $dimensiJawaban,
            ]
        ]);
    }

    /**
     * Tandai notifikasi sebagai sudah dibaca
     */
    public function markNotificationRead(int $id): JsonResponse
    {
        $notif = Notifikasi::findOrFail($id);
        if ($notif->target_user_id !== null && $notif->target_user_id !== Auth::id()) {
            abort(403);
        }
        $notif->is_read = true;
        $notif->save();
        return response()->json(['success' => true]);
    }

    /**
     * Tandai semua notifikasi sebagai sudah dibaca
     */
    public function markAllNotificationsRead(): JsonResponse
    {
        Notifikasi::where(function ($q) {
            $q->where('target_role', 'dosen')
                ->orWhere('target_role', 'all')
                ->orWhere('target_user_id', Auth::id());
        })->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }
}