<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PenilaianDosen;
use App\Models\User;
use App\Models\Pertanyaan;
use App\Models\Jurusan;
use App\Models\KuesionerPeriode;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanExport;

class LaporanController extends Controller
{
    /**
     * Halaman utama laporan dengan filter
     */
    public function index(Request $request)
    {
        $periodeId = $request->input('periode_id');
        $periodeTerpilih = null;

        if ($periodeId) {
            $periodeTerpilih = KuesionerPeriode::find($periodeId);
        }

        $data = $this->getFilteredLaporan($request, $periodeTerpilih);
        $dosenList = User::where('role', 'dosen')->where('is_active', true)->orderBy('name')->get();
        $jurusanList = Jurusan::orderBy('nama_jurusan')->get();
        $periodeList = KuesionerPeriode::orderBy('created_at', 'desc')->get();
        $statistik = $this->getStatistik($request, $periodeTerpilih);

        return view('admin.laporan.index', compact(
            'data',
            'dosenList',
            'jurusanList',
            'statistik',
            'periodeList',
            'periodeTerpilih'
        ));
    }

    private function getFilteredLaporan(Request $request, $periodeTerpilih = null)
    {
        $query = PenilaianDosen::with(['dosen', 'mahasiswa']);

        if ($periodeTerpilih)
            $query->where('periode_id', $periodeTerpilih->id);
        if ($request->filled('dosen_id'))
            $query->where('dosen_id', $request->dosen_id);
        if ($request->filled('jurusan_id')) {
            $jurusan = Jurusan::find($request->jurusan_id);
            if ($jurusan) {
                $query->whereHas('dosen', fn($q) => $q->where('jurusan', $jurusan->nama_jurusan));
            }
        }
        if ($request->filled('start_date'))
            $query->whereDate('created_at', '>=', $request->start_date);
        if ($request->filled('end_date'))
            $query->whereDate('created_at', '<=', $request->end_date);
        if ($request->filled('min_rating'))
            $query->where('rata_rata', '>=', $request->min_rating);
        if ($request->filled('max_rating'))
            $query->where('rata_rata', '<=', $request->max_rating);

        return $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
    }

    private function getStatistik(Request $request, $periodeTerpilih = null)
    {
        $query = PenilaianDosen::query();
        if ($periodeTerpilih)
            $query->where('periode_id', $periodeTerpilih->id);
        if ($request->filled('dosen_id'))
            $query->where('dosen_id', $request->dosen_id);
        if ($request->filled('start_date'))
            $query->whereDate('created_at', '>=', $request->start_date);
        if ($request->filled('end_date'))
            $query->whereDate('created_at', '<=', $request->end_date);

        return [
            'total_penilaian' => $query->count(),
            'rata_rata' => round($query->avg('rata_rata') ?? 0, 2),
            'tertinggi' => round($query->max('rata_rata') ?? 0, 2),
            'terendah' => round($query->min('rata_rata') ?? 0, 2),
        ];
    }

    public function chartData(Request $request)
    {
        $periodeId = $request->input('periode_id');
        $periodeTerpilih = $periodeId ? KuesionerPeriode::find($periodeId) : null;
        $dimensi = ['Tangible', 'Reliability', 'Responsiveness', 'Assurance', 'Empathy'];
        $result = [];

        $query = PenilaianDosen::query();
        if ($periodeTerpilih)
            $query->where('periode_id', $periodeTerpilih->id);
        if ($request->filled('dosen_id'))
            $query->where('dosen_id', $request->dosen_id);

        $penilaianList = $query->get();

        if ($penilaianList->isEmpty()) {
            foreach ($dimensi as $dim)
                $result[$dim] = ['persepsi' => 0, 'harapan' => 0, 'gap' => 0];
            return response()->json($result);
        }

        $totalPersepsi = $totalHarapan = $count = array_fill_keys($dimensi, 0);

        foreach ($penilaianList as $penilaian) {
            $nilai = json_decode($penilaian->nilai, true);
            if (is_array($nilai)) {
                foreach ($nilai as $item) {
                    if (isset($item['id_pertanyaan'])) {
                        $pertanyaan = Pertanyaan::find($item['id_pertanyaan']);
                        $dim = $pertanyaan ? $pertanyaan->dimensi : null;
                        if ($dim && in_array($dim, $dimensi)) {
                            $totalPersepsi[$dim] += $item['persepsi'] ?? 0;
                            $totalHarapan[$dim] += $item['harapan'] ?? 0;
                            $count[$dim]++;
                        }
                    }
                }
            }
        }

        foreach ($dimensi as $dim) {
            $persepsi = $count[$dim] > 0 ? round($totalPersepsi[$dim] / $count[$dim], 2) : 0;
            $harapan = $count[$dim] > 0 ? round($totalHarapan[$dim] / $count[$dim], 2) : 0;
            $result[$dim] = ['persepsi' => $persepsi, 'harapan' => $harapan, 'gap' => round($persepsi - $harapan, 2)];
        }

        return response()->json($result);
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new LaporanExport($request), 'laporan_penilaian_dosen.xlsx');
    }

    public function detailDosen(int $dosenId)
    {
        $dosen = User::with('prodi.jurusan')->findOrFail($dosenId);
        $penilaian = PenilaianDosen::where('dosen_id', $dosenId)->orderBy('created_at', 'desc')->paginate(15);

        $statistik = [
            'total' => PenilaianDosen::where('dosen_id', $dosenId)->count(),
            'rata_rata' => round(PenilaianDosen::where('dosen_id', $dosenId)->avg('rata_rata') ?? 0, 2),
            'tertinggi' => round(PenilaianDosen::where('dosen_id', $dosenId)->max('rata_rata') ?? 0, 2),
            'terendah' => round(PenilaianDosen::where('dosen_id', $dosenId)->min('rata_rata') ?? 0, 2),
        ];

        $chartData = $this->getChartDataForDosen($dosenId);
        return view('admin.laporan.detail_dosen', compact('dosen', 'penilaian', 'statistik', 'chartData'));
    }

    private function getChartDataForDosen(int $dosenId): array
    {
        $dimensi = ['Tangible', 'Reliability', 'Responsiveness', 'Assurance', 'Empathy'];
        $result = [];
        $penilaianList = PenilaianDosen::where('dosen_id', $dosenId)->get();

        if ($penilaianList->isEmpty()) {
            foreach ($dimensi as $dim)
                $result[$dim] = ['persepsi' => 0, 'harapan' => 0, 'gap' => 0];
            return $result;
        }

        $totalPersepsi = $totalHarapan = $count = array_fill_keys($dimensi, 0);

        foreach ($penilaianList as $penilaian) {
            $nilai = json_decode($penilaian->nilai, true);
            if (is_array($nilai)) {
                foreach ($nilai as $item) {
                    if (isset($item['id_pertanyaan'])) {
                        $pertanyaan = Pertanyaan::find($item['id_pertanyaan']);
                        $dim = $pertanyaan ? $pertanyaan->dimensi : null;
                        if ($dim && in_array($dim, $dimensi)) {
                            $totalPersepsi[$dim] += $item['persepsi'] ?? 0;
                            $totalHarapan[$dim] += $item['harapan'] ?? 0;
                            $count[$dim]++;
                        }
                    }
                }
            }
        }

        foreach ($dimensi as $dim) {
            $persepsi = $count[$dim] > 0 ? round($totalPersepsi[$dim] / $count[$dim], 2) : 0;
            $harapan = $count[$dim] > 0 ? round($totalHarapan[$dim] / $count[$dim], 2) : 0;
            $result[$dim] = ['persepsi' => $persepsi, 'harapan' => $harapan, 'gap' => round($persepsi - $harapan, 2)];
        }

        return $result;
    }

    public function getDetailJawaban(int $penilaianId)
    {
        $penilaian = PenilaianDosen::findOrFail($penilaianId);
        $nilai = json_decode($penilaian->nilai, true);
        $detailJawaban = [];

        if (is_array($nilai)) {
            ksort($nilai);
            foreach ($nilai as $key => $item) {
                if (is_array($item)) {
                    $idPertanyaan = $item['id_pertanyaan'] ?? $key;
                    $pertanyaan = Pertanyaan::find($idPertanyaan);
                    $detailJawaban[] = [
                        'no' => count($detailJawaban) + 1,
                        'pertanyaan' => $pertanyaan ? $pertanyaan->teks : 'Pertanyaan ' . $idPertanyaan,
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
                'mahasiswa' => $penilaian->mahasiswa_nama,
                'nim' => $penilaian->mahasiswa_nim,
                'kelas' => $penilaian->kelas ?? '-',
                'mata_kuliah' => $penilaian->mata_kuliah ?? '-',
                'rata_rata' => $penilaian->rata_rata,
                'tanggal' => $penilaian->created_at->format('d/m/Y H:i'),
                'jawaban' => $detailJawaban,
            ]
        ]);
    }
}