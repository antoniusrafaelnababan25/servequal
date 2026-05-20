<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\PenilaianDosen;
use App\Models\Pertanyaan;
use App\Models\KuesionerPeriode;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PenilaianMahasiswaExport;

class PenilaianMahasiswaController extends Controller
{
    /**
     * Daftar mahasiswa yang menilai dosen ini
     */
    public function index(Request $request)
    {
        $dosenId = Auth::id();

        $query = PenilaianDosen::where('dosen_id', $dosenId)->with('mahasiswa', 'periode');

        // Filter berdasarkan nama mahasiswa / nim
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('mahasiswa_nama', 'like', "%{$search}%")
                    ->orWhere('mahasiswa_nim', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan periode
        if ($request->filled('periode_id')) {
            $query->where('periode_id', $request->periode_id);
        }

        // Filter berdasarkan kelas
        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }

        // Filter berdasarkan rentang nilai
        if ($request->filled('min_rating')) {
            $query->where('rata_rata', '>=', $request->min_rating);
        }
        if ($request->filled('max_rating')) {
            $query->where('rata_rata', '<=', $request->max_rating);
        }

        // Filter tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $penilaian = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Statistik
        $statistik = [
            'total' => PenilaianDosen::where('dosen_id', $dosenId)->count(),
            'rata_rata' => round(PenilaianDosen::where('dosen_id', $dosenId)->avg('rata_rata') ?? 0, 2),
            'tertinggi' => round(PenilaianDosen::where('dosen_id', $dosenId)->max('rata_rata') ?? 0, 2),
            'terendah' => round(PenilaianDosen::where('dosen_id', $dosenId)->min('rata_rata') ?? 0, 2),
        ];

        // Data untuk filter
        $kelasList = PenilaianDosen::where('dosen_id', $dosenId)
            ->whereNotNull('kelas')
            ->distinct()
            ->pluck('kelas');

        $periodeList = KuesionerPeriode::orderBy('created_at', 'desc')->get();

        return view('dosen.penilaian-mahasiswa.index', compact('penilaian', 'kelasList', 'periodeList', 'statistik'));
    }

    /**
     * Detail penilaian dari seorang mahasiswa (AJAX JSON)
     */
    public function getDetailJawaban(int $id): JsonResponse
    {
        try {
            $dosenId = Auth::id();
            $penilaian = PenilaianDosen::where('dosen_id', $dosenId)
                ->with('mahasiswa', 'periode')
                ->findOrFail($id);

            $nilai = $penilaian->nilai;
            if (is_string($nilai)) {
                $nilai = json_decode($nilai, true);
            }

            $detailJawaban = $this->parseJawaban($nilai);
            $dimensiJawaban = $this->groupByDimensi($detailJawaban);

            return response()->json([
                'success' => true,
                'data' => [
                    'mahasiswa' => $penilaian->mahasiswa->name ?? $penilaian->mahasiswa_nama ?? '-',
                    'nim' => $penilaian->mahasiswa->nim ?? $penilaian->mahasiswa_nim ?? '-',
                    'kelas' => $penilaian->kelas ?? '-',
                    'mata_kuliah' => $penilaian->mata_kuliah ?? '-',
                    'periode' => $penilaian->periode ? $penilaian->periode->nama_periode : '-',
                    'rata_rata' => $penilaian->rata_rata,
                    'tanggal' => $penilaian->created_at->format('d/m/Y H:i'),
                    'jawaban' => $detailJawaban,
                    'dimensi_jawaban' => $dimensiJawaban,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Parse jawaban dari JSON
     */
    private function parseJawaban($nilai): array
    {
        $detailJawaban = [];

        if (!is_array($nilai)) {
            return $detailJawaban;
        }

        if (isset($nilai[0]) && is_array($nilai[0])) {
            foreach ($nilai as $index => $item) {
                $idPertanyaan = $item['id_pertanyaan'] ?? ($index + 1);
                $pertanyaan = Pertanyaan::find($idPertanyaan);

                $detailJawaban[] = [
                    'no' => $index + 1,
                    'pertanyaan' => $pertanyaan ? $pertanyaan->teks : ($item['teks'] ?? 'Pertanyaan ' . ($index + 1)),
                    'dimensi' => $pertanyaan ? $pertanyaan->dimensi : ($item['dimensi'] ?? '-'),
                    'harapan' => (int) ($item['harapan'] ?? 0),
                    'persepsi' => (int) ($item['persepsi'] ?? 0),
                    'gap' => ((int) ($item['persepsi'] ?? 0)) - ((int) ($item['harapan'] ?? 0)),
                ];
            }
        } else {
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
                    'harapan' => is_array($item) ? (int) ($item['harapan'] ?? 0) : 0,
                    'persepsi' => is_array($item) ? (int) ($item['persepsi'] ?? 0) : 0,
                    'gap' => (is_array($item) ? (int) ($item['persepsi'] ?? 0) : 0) - (is_array($item) ? (int) ($item['harapan'] ?? 0) : 0),
                ];
            }
        }

        return $detailJawaban;
    }

    /**
     * Kelompokkan jawaban berdasarkan dimensi
     */
    private function groupByDimensi(array $jawaban): array
    {
        $dimensiJawaban = [];
        foreach ($jawaban as $item) {
            $dimensi = $item['dimensi'];
            if (!isset($dimensiJawaban[$dimensi])) {
                $dimensiJawaban[$dimensi] = [];
            }
            $dimensiJawaban[$dimensi][] = $item;
        }
        return $dimensiJawaban;
    }

    /**
     * Export Excel
     */
    public function exportExcel(Request $request)
    {
        $dosenId = Auth::id();
        return Excel::download(new PenilaianMahasiswaExport($request, $dosenId), 'penilaian_mahasiswa_saya.xlsx');
    }
}