<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\PenilaianDosen;
use App\Models\User;
use App\Models\Pertanyaan;
use App\Models\Jurusan;
use App\Models\KuesionerPeriode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanExport;

class LaporanController extends Controller
{
    /**
     * Halaman utama laporan dengan filter
     */
    public function index(Request $request)
    {
        // Ambil periode yang dipilih dari request
        $periodeId = $request->input('periode_id');
        $periodeTerpilih = null;

        if ($periodeId) {
            $periodeTerpilih = KuesionerPeriode::find($periodeId);
        }

        // Data laporan dengan filter periode
        $data = $this->getFilteredLaporan($request, $periodeTerpilih);

        // Data untuk dropdown filter
        $dosenList = User::where('role', 'dosen')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $jurusanList = Jurusan::orderBy('nama_jurusan')->get();
        $dimensi = ['Tangible', 'Reliability', 'Responsiveness', 'Assurance', 'Empathy'];

        // Daftar periode untuk dropdown
        $periodeList = KuesionerPeriode::orderBy('created_at', 'desc')->get();

        // Statistik ringkasan berdasarkan periode
        $statistik = $this->getStatistik($request, $periodeTerpilih);

        return view('superadmin.laporan.index', compact(
            'data',
            'dosenList',
            'jurusanList',
            'dimensi',
            'statistik',
            'periodeList',
            'periodeTerpilih'
        ));
    }

    /**
     * Filter laporan berdasarkan request dan periode
     */
    private function getFilteredLaporan(Request $request, $periodeTerpilih = null)
    {
        $query = PenilaianDosen::with(['dosen', 'mahasiswa', 'periode']);

        // Filter berdasarkan periode
        if ($periodeTerpilih) {
            $query->where('periode_id', $periodeTerpilih->id);
        }

        // Filter berdasarkan dosen
        if ($request->filled('dosen_id')) {
            $query->where('dosen_id', $request->dosen_id);
        }

        // Filter berdasarkan jurusan
        if ($request->filled('jurusan_id')) {
            $jurusan = Jurusan::find($request->jurusan_id);
            if ($jurusan) {
                $query->whereHas('dosen', function ($q) use ($jurusan) {
                    $q->where('jurusan', $jurusan->id);
                });
            }
        }

        // Filter berdasarkan prodi
        if ($request->filled('prodi_id')) {
            $query->whereHas('dosen', function ($q) use ($request) {
                $q->where('prodi_id', $request->prodi_id);
            });
        }

        // Filter berdasarkan tanggal mulai
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        // Filter berdasarkan tanggal selesai
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Filter berdasarkan rentang nilai
        if ($request->filled('min_rating')) {
            $query->where('rata_rata', '>=', $request->min_rating);
        }
        if ($request->filled('max_rating')) {
            $query->where('rata_rata', '<=', $request->max_rating);
        }

        return $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
    }

    /**
     * Get statistik ringkasan berdasarkan periode
     */
    private function getStatistik(Request $request, $periodeTerpilih = null)
    {
        $query = PenilaianDosen::query();

        // Filter berdasarkan periode
        if ($periodeTerpilih) {
            $query->where('periode_id', $periodeTerpilih->id);
        }

        if ($request->filled('dosen_id')) {
            $query->where('dosen_id', $request->dosen_id);
        }
        if ($request->filled('jurusan_id')) {
            $jurusan = Jurusan::find($request->jurusan_id);
            if ($jurusan) {
                $query->whereHas('dosen', function ($q) use ($jurusan) {
                    $q->where('jurusan', $jurusan->id);
                });
            }
        }
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        return [
            'total_penilaian' => $query->count(),
            'total_dosen' => $query->distinct('dosen_id')->count('dosen_id'),
            'total_mahasiswa' => $query->distinct('mahasiswa_id')->count('mahasiswa_id'),
            'rata_rata' => round($query->avg('rata_rata') ?? 0, 2),
            'tertinggi' => round($query->max('rata_rata') ?? 0, 2),
            'terendah' => round($query->min('rata_rata') ?? 0, 2),
        ];
    }

    /**
     * API: Data chart gap per dimensi (dengan filter periode)
     */
    public function chartData(Request $request)
    {
        $periodeId = $request->input('periode_id');
        $periodeTerpilih = $periodeId ? KuesionerPeriode::find($periodeId) : null;

        $dimensi = ['Tangible', 'Reliability', 'Responsiveness', 'Assurance', 'Empathy'];
        $result = [];

        // Ambil semua penilaian sesuai filter
        $query = PenilaianDosen::query();

        if ($periodeTerpilih) {
            $query->where('periode_id', $periodeTerpilih->id);
        }

        if ($request->filled('dosen_id')) {
            $query->where('dosen_id', $request->dosen_id);
        }
        if ($request->filled('jurusan_id')) {
            $jurusan = Jurusan::find($request->jurusan_id);
            if ($jurusan) {
                $query->whereHas('dosen', function ($q) use ($jurusan) {
                    $q->where('jurusan', $jurusan->id);
                });
            }
        }
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $penilaianList = $query->get();

        // Jika tidak ada data, return default
        if ($penilaianList->isEmpty()) {
            foreach ($dimensi as $dim) {
                $result[$dim] = [
                    'persepsi' => 0,
                    'harapan' => 0,
                    'gap' => 0,
                ];
            }
            return response()->json([
                'success' => true,
                'data' => $result,
                'chart' => [
                    'labels' => $dimensi,
                    'persepsi' => array_fill(0, 5, 0),
                    'harapan' => array_fill(0, 5, 0),
                    'gap' => array_fill(0, 5, 0),
                ]
            ]);
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

            if (is_array($nilai)) {
                foreach ($nilai as $item) {
                    $dimensiItem = $this->getDimensiFromItem($item);
                    if ($dimensiItem && in_array($dimensiItem, $dimensi)) {
                        $totalPersepsi[$dimensiItem] += $item['persepsi'] ?? 0;
                        $totalHarapan[$dimensiItem] += $item['harapan'] ?? 0;
                        $count[$dimensiItem]++;
                    }
                }
            }
        }

        // Hitung rata-rata
        $chartPersepsi = [];
        $chartHarapan = [];
        $chartGap = [];

        foreach ($dimensi as $dim) {
            $avgPersepsi = $count[$dim] > 0 ? round($totalPersepsi[$dim] / $count[$dim], 2) : 0;
            $avgHarapan = $count[$dim] > 0 ? round($totalHarapan[$dim] / $count[$dim], 2) : 0;
            $result[$dim] = [
                'persepsi' => $avgPersepsi,
                'harapan' => $avgHarapan,
                'gap' => round($avgPersepsi - $avgHarapan, 2),
            ];
            $chartPersepsi[] = $avgPersepsi;
            $chartHarapan[] = $avgHarapan;
            $chartGap[] = round($avgPersepsi - $avgHarapan, 2);
        }

        return response()->json([
            'success' => true,
            'data' => $result,
            'chart' => [
                'labels' => $dimensi,
                'persepsi' => $chartPersepsi,
                'harapan' => $chartHarapan,
                'gap' => $chartGap,
            ]
        ]);
    }

    /**
     * Helper: Mendapatkan dimensi dari item jawaban
     */
    private function getDimensiFromItem(array $item): ?string
    {
        // Jika ada field 'dimensi'
        if (isset($item['dimensi'])) {
            return $item['dimensi'];
        }

        // Jika ada 'id_pertanyaan', cari dari database
        if (isset($item['id_pertanyaan'])) {
            $pertanyaan = Pertanyaan::find($item['id_pertanyaan']);
            if ($pertanyaan) {
                return $pertanyaan->dimensi;
            }
        }

        return null;
    }

    /**
     * Export laporan ke Excel (dengan filter periode)
     */
    public function exportExcel(Request $request)
    {
        return Excel::download(new LaporanExport($request), 'laporan_penilaian_dosen_superadmin.xlsx');
    }

    /**
     * Detail penilaian untuk satu dosen
     */
    public function detailDosen(int $dosenId, Request $request)
    {
        $dosen = User::with('prodi.jurusan')->findOrFail($dosenId);

        $periodeId = $request->input('periode_id');
        $periodeTerpilih = $periodeId ? KuesionerPeriode::find($periodeId) : null;

        $query = PenilaianDosen::where('dosen_id', $dosenId)->with('mahasiswa');

        if ($periodeTerpilih) {
            $query->where('periode_id', $periodeTerpilih->id);
        }

        $penilaian = $query->orderBy('created_at', 'desc')->paginate(15);

        // Statistik dosen
        $statistikQuery = PenilaianDosen::where('dosen_id', $dosenId);
        if ($periodeTerpilih) {
            $statistikQuery->where('periode_id', $periodeTerpilih->id);
        }

        $statistik = [
            'total' => $statistikQuery->count(),
            'rata_rata' => round($statistikQuery->avg('rata_rata') ?? 0, 2),
            'tertinggi' => round($statistikQuery->max('rata_rata') ?? 0, 2),
            'terendah' => round($statistikQuery->min('rata_rata') ?? 0, 2),
        ];

        // Chart per dimensi untuk dosen ini
        $chartData = $this->getChartDataForDosen($dosenId, $periodeTerpilih);

        $periodeList = KuesionerPeriode::orderBy('created_at', 'desc')->get();

        return view('superadmin.laporan.detail_dosen', compact(
            'dosen',
            'penilaian',
            'statistik',
            'chartData',
            'periodeList',
            'periodeTerpilih'
        ));
    }

    /**
     * Get chart data untuk satu dosen
     */
    private function getChartDataForDosen(int $dosenId, $periodeTerpilih = null): array
    {
        $dimensi = ['Tangible', 'Reliability', 'Responsiveness', 'Assurance', 'Empathy'];
        $result = [];

        $query = PenilaianDosen::where('dosen_id', $dosenId);
        if ($periodeTerpilih) {
            $query->where('periode_id', $periodeTerpilih->id);
        }
        $penilaianList = $query->get();

        if ($penilaianList->isEmpty()) {
            foreach ($dimensi as $dim) {
                $result[$dim] = ['persepsi' => 0, 'harapan' => 0, 'gap' => 0];
            }
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
            if (is_array($nilai)) {
                foreach ($nilai as $item) {
                    $dimensiItem = $this->getDimensiFromItem($item);
                    if ($dimensiItem && in_array($dimensiItem, $dimensi)) {
                        $totalPersepsi[$dimensiItem] += $item['persepsi'] ?? 0;
                        $totalHarapan[$dimensiItem] += $item['harapan'] ?? 0;
                        $count[$dimensiItem]++;
                    }
                }
            }
        }

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
     * API: Detail jawaban per penilaian (AJAX)
     */
    public function getDetailJawaban(int $penilaianId)
    {
        $penilaian = PenilaianDosen::with(['dosen', 'mahasiswa'])->findOrFail($penilaianId);

        $nilai = $penilaian->nilai;

        // Decode jika string
        if (is_string($nilai)) {
            $nilai = json_decode($nilai, true);
        }

        $detailJawaban = [];

        // Format: {"1":{"harapan":"5","persepsi":"4","id_pertanyaan":"1"}, ...}
        if (is_array($nilai)) {
            // Urutkan berdasarkan key
            ksort($nilai);

            foreach ($nilai as $key => $item) {
                if (is_array($item)) {
                    $idPertanyaan = $item['id_pertanyaan'] ?? $key;
                    $pertanyaan = Pertanyaan::find($idPertanyaan);

                    $detailJawaban[] = [
                        'no' => count($detailJawaban) + 1,
                        'pertanyaan' => $pertanyaan ? $pertanyaan->teks : ('Pertanyaan ' . $idPertanyaan),
                        'dimensi' => $pertanyaan ? $pertanyaan->dimensi : '-',
                        'harapan' => intval($item['harapan'] ?? 0),
                        'persepsi' => intval($item['persepsi'] ?? 0),
                        'gap' => intval($item['persepsi'] ?? 0) - intval($item['harapan'] ?? 0),
                    ];
                }
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'dosen' => $penilaian->dosen ? $penilaian->dosen->name : '-',
                'nidn' => $penilaian->dosen ? $penilaian->dosen->nidn : '-',
                'mahasiswa' => $penilaian->mahasiswa ? $penilaian->mahasiswa->name : '-',
                'nim' => $penilaian->mahasiswa ? $penilaian->mahasiswa->nim : '-',
                'kelas' => $penilaian->kelas ?? '-',
                'mata_kuliah' => $penilaian->mata_kuliah ?? '-',
                'rata_rata' => $penilaian->rata_rata,
                'tanggal' => $penilaian->created_at->format('d/m/Y H:i'),
                'jawaban' => $detailJawaban,
            ]
        ]);
    }

    /**
     * API: Get statistik per periode (AJAX)
     */
    public function statistikPeriode(Request $request)
    {
        $periodeId = $request->input('periode_id');

        if (!$periodeId) {
            return response()->json([
                'success' => false,
                'message' => 'Periode tidak dipilih'
            ]);
        }

        $periode = KuesionerPeriode::find($periodeId);

        if (!$periode) {
            return response()->json([
                'success' => false,
                'message' => 'Periode tidak ditemukan'
            ]);
        }

        $statistik = [
            'total_penilaian' => PenilaianDosen::where('periode_id', $periodeId)->count(),
            'total_dosen' => PenilaianDosen::where('periode_id', $periodeId)->distinct('dosen_id')->count('dosen_id'),
            'total_mahasiswa' => PenilaianDosen::where('periode_id', $periodeId)->distinct('mahasiswa_id')->count('mahasiswa_id'),
            'rata_rata' => round(PenilaianDosen::where('periode_id', $periodeId)->avg('rata_rata') ?? 0, 2),
        ];

        // Rata-rata per dimensi
        $dimensi = ['Tangible', 'Reliability', 'Responsiveness', 'Assurance', 'Empathy'];
        $rataDimensi = [];

        $penilaianList = PenilaianDosen::where('periode_id', $periodeId)->get();

        if ($penilaianList->isNotEmpty()) {
            $totalPersepsi = array_fill_keys($dimensi, 0);
            $totalHarapan = array_fill_keys($dimensi, 0);
            $count = array_fill_keys($dimensi, 0);

            foreach ($penilaianList as $penilaian) {
                $nilai = $penilaian->nilai;
                if (is_string($nilai)) {
                    $nilai = json_decode($nilai, true);
                }
                if (is_array($nilai)) {
                    foreach ($nilai as $item) {
                        $dimensiItem = $this->getDimensiFromItem($item);
                        if ($dimensiItem && in_array($dimensiItem, $dimensi)) {
                            $totalPersepsi[$dimensiItem] += $item['persepsi'] ?? 0;
                            $totalHarapan[$dimensiItem] += $item['harapan'] ?? 0;
                            $count[$dimensiItem]++;
                        }
                    }
                }
            }

            foreach ($dimensi as $dim) {
                $rataDimensi[$dim] = [
                    'persepsi' => $count[$dim] > 0 ? round($totalPersepsi[$dim] / $count[$dim], 2) : 0,
                    'harapan' => $count[$dim] > 0 ? round($totalHarapan[$dim] / $count[$dim], 2) : 0,
                ];
            }
        } else {
            foreach ($dimensi as $dim) {
                $rataDimensi[$dim] = ['persepsi' => 0, 'harapan' => 0];
            }
        }

        return response()->json([
            'success' => true,
            'statistik' => $statistik,
            'rata_dimensi' => $rataDimensi,
            'periode' => $periode
        ]);
    }
}