<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PenilaianFasilitas;
use App\Models\User;
use App\Models\Pertanyaan;
use App\Models\Jurusan;
use App\Models\Prodi;
use App\Models\KuesionerPeriode;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanFasilitasExport;
use Illuminate\Support\Facades\Log;

class LaporanFasilitasController extends Controller
{
    /**
     * Halaman utama laporan fasilitas
     */
    public function index(Request $request)
    {
        $periodeId = $request->input('periode_id');
        $periodeTerpilih = $periodeId ? KuesionerPeriode::find($periodeId) : null;

        $data = $this->getFilteredLaporan($request, $periodeTerpilih);
        $mahasiswaList = User::where('role', 'mahasiswa')->where('is_active', true)->orderBy('name')->get();
        $jurusanList = Jurusan::orderBy('nama_jurusan')->get();
        $prodiList = Prodi::with('jurusan')->orderBy('nama_prodi')->get();
        $periodeList = KuesionerPeriode::orderBy('created_at', 'desc')->get();
        $statistik = $this->getStatistik($request, $periodeTerpilih);
        $chartData = $this->getChartData($request, $periodeTerpilih);

        return view('admin.laporan.fasilitas.index', compact(
            'data',
            'mahasiswaList',
            'jurusanList',
            'prodiList',
            'statistik',
            'periodeList',
            'periodeTerpilih',
            'chartData'
        ));
    }

    /**
     * Filter laporan berdasarkan request
     */
    private function getFilteredLaporan(Request $request, $periodeTerpilih = null)
    {
        $query = PenilaianFasilitas::with('mahasiswa');

        if ($periodeTerpilih) {
            $query->where('periode_id', $periodeTerpilih->id);
        }
        if ($request->filled('mahasiswa_id')) {
            $query->where('mahasiswa_id', $request->mahasiswa_id);
        }
        if ($request->filled('jurusan_id')) {
            $jurusan = Jurusan::find($request->jurusan_id);
            if ($jurusan) {
                $query->whereHas('mahasiswa', function ($q) use ($jurusan) {
                    $q->where('jurusan', $jurusan->nama_jurusan);
                });
            }
        }
        if ($request->filled('prodi_id')) {
            $query->whereHas('mahasiswa', function ($q) use ($request) {
                $q->where('prodi_id', $request->prodi_id);
            });
        }
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->filled('min_rating')) {
            $query->where('rata_rata', '>=', $request->min_rating);
        }
        if ($request->filled('max_rating')) {
            $query->where('rata_rata', '<=', $request->max_rating);
        }

        return $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
    }

    /**
     * Statistik ringkasan
     */
    private function getStatistik(Request $request, $periodeTerpilih = null)
    {
        $query = PenilaianFasilitas::query();

        if ($periodeTerpilih) {
            $query->where('periode_id', $periodeTerpilih->id);
        }
        if ($request->filled('mahasiswa_id')) {
            $query->where('mahasiswa_id', $request->mahasiswa_id);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        return [
            'total_penilaian' => $query->count(),
            'rata_rata' => round($query->avg('rata_rata') ?? 0, 2),
            'tertinggi' => round($query->max('rata_rata') ?? 0, 2),
            'terendah' => round($query->min('rata_rata') ?? 0, 2),
        ];
    }

    /**
     * Data chart untuk grafik
     */
    public function getChartData(Request $request, $periodeTerpilih = null)
    {
        $kategori = ['umum', 'peralatan', 'ruangan', 'akses', 'infrastruktur'];
        $kategoriLabel = [
            'umum' => 'Umum',
            'peralatan' => 'Peralatan',
            'ruangan' => 'Ruangan',
            'akses' => 'Akses',
            'infrastruktur' => 'Infrastruktur'
        ];

        $result = [];

        $query = PenilaianFasilitas::query();

        if ($periodeTerpilih) {
            $query->where('periode_id', $periodeTerpilih->id);
        }
        if ($request->filled('mahasiswa_id')) {
            $query->where('mahasiswa_id', $request->mahasiswa_id);
        }

        $penilaianList = $query->get();

        if ($penilaianList->isEmpty()) {
            foreach ($kategori as $kat) {
                $result[$kat] = [
                    'persepsi' => 0,
                    'harapan' => 0,
                    'gap' => 0,
                    'label' => $kategoriLabel[$kat]
                ];
            }
            return $result;
        }

        $totalPersepsi = array_fill_keys($kategori, 0);
        $totalHarapan = array_fill_keys($kategori, 0);
        $count = array_fill_keys($kategori, 0);

        foreach ($penilaianList as $penilaian) {
            $nilai = $penilaian->nilai;
            $dataJawaban = is_string($nilai) ? json_decode($nilai, true) : $nilai;

            if (is_array($dataJawaban)) {
                foreach ($dataJawaban as $item) {
                    if (isset($item['id_pertanyaan'])) {
                        $pertanyaan = Pertanyaan::find($item['id_pertanyaan']);
                        $kat = $pertanyaan ? $pertanyaan->kategori_fasilitas : null;
                        if ($kat && in_array($kat, $kategori)) {
                            $totalPersepsi[$kat] += $item['persepsi'] ?? 0;
                            $totalHarapan[$kat] += $item['harapan'] ?? 0;
                            $count[$kat]++;
                        }
                    }
                }
            }
        }

        foreach ($kategori as $kat) {
            $persepsi = $count[$kat] > 0 ? round($totalPersepsi[$kat] / $count[$kat], 2) : 0;
            $harapan = $count[$kat] > 0 ? round($totalHarapan[$kat] / $count[$kat], 2) : 0;
            $result[$kat] = [
                'persepsi' => $persepsi,
                'harapan' => $harapan,
                'gap' => round($persepsi - $harapan, 2),
                'label' => $kategoriLabel[$kat]
            ];
        }

        return $result;
    }

    /**
     * API Chart Data
     */
    public function chartData(Request $request)
    {
        $periodeId = $request->input('periode_id');
        $periodeTerpilih = $periodeId ? KuesionerPeriode::find($periodeId) : null;
        return response()->json($this->getChartData($request, $periodeTerpilih));
    }

    /**
     * Export ke Excel
     */
    public function exportExcel(Request $request)
    {
        return Excel::download(new LaporanFasilitasExport($request), 'laporan_penilaian_fasilitas.xlsx');
    }

    /**
     * Detail per mahasiswa
     */
    public function detailMahasiswa(int $mahasiswaId)
    {
        $mahasiswa = User::with('prodi.jurusan')->findOrFail($mahasiswaId);
        $penilaian = PenilaianFasilitas::where('mahasiswa_id', $mahasiswaId)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $statistik = [
            'total' => PenilaianFasilitas::where('mahasiswa_id', $mahasiswaId)->count(),
            'rata_rata' => round(PenilaianFasilitas::where('mahasiswa_id', $mahasiswaId)->avg('rata_rata') ?? 0, 2),
            'tertinggi' => round(PenilaianFasilitas::where('mahasiswa_id', $mahasiswaId)->max('rata_rata') ?? 0, 2),
            'terendah' => round(PenilaianFasilitas::where('mahasiswa_id', $mahasiswaId)->min('rata_rata') ?? 0, 2),
        ];

        $chartData = $this->getChartDataForMahasiswa($mahasiswaId);

        return view('admin.laporan.fasilitas.detail_mahasiswa', compact(
            'mahasiswa',
            'penilaian',
            'statistik',
            'chartData'
        ));
    }

    /**
     * Chart data untuk satu mahasiswa
     */
    private function getChartDataForMahasiswa(int $mahasiswaId): array
    {
        $kategori = ['umum', 'peralatan', 'ruangan', 'akses', 'infrastruktur'];
        $kategoriLabel = [
            'umum' => 'Umum',
            'peralatan' => 'Peralatan',
            'ruangan' => 'Ruangan',
            'akses' => 'Akses',
            'infrastruktur' => 'Infrastruktur'
        ];
        $result = [];

        $penilaianList = PenilaianFasilitas::where('mahasiswa_id', $mahasiswaId)->get();

        if ($penilaianList->isEmpty()) {
            foreach ($kategori as $kat) {
                $result[$kat] = [
                    'persepsi' => 0,
                    'harapan' => 0,
                    'gap' => 0,
                    'label' => $kategoriLabel[$kat]
                ];
            }
            return $result;
        }

        $totalPersepsi = array_fill_keys($kategori, 0);
        $totalHarapan = array_fill_keys($kategori, 0);
        $count = array_fill_keys($kategori, 0);

        foreach ($penilaianList as $penilaian) {
            $nilai = $penilaian->nilai;
            $dataJawaban = is_string($nilai) ? json_decode($nilai, true) : $nilai;

            if (is_array($dataJawaban)) {
                foreach ($dataJawaban as $item) {
                    if (isset($item['id_pertanyaan'])) {
                        $pertanyaan = Pertanyaan::find($item['id_pertanyaan']);
                        $kat = $pertanyaan ? $pertanyaan->kategori_fasilitas : null;
                        if ($kat && in_array($kat, $kategori)) {
                            $totalPersepsi[$kat] += $item['persepsi'] ?? 0;
                            $totalHarapan[$kat] += $item['harapan'] ?? 0;
                            $count[$kat]++;
                        }
                    }
                }
            }
        }

        foreach ($kategori as $kat) {
            $persepsi = $count[$kat] > 0 ? round($totalPersepsi[$kat] / $count[$kat], 2) : 0;
            $harapan = $count[$kat] > 0 ? round($totalHarapan[$kat] / $count[$kat], 2) : 0;
            $result[$kat] = [
                'persepsi' => $persepsi,
                'harapan' => $harapan,
                'gap' => round($persepsi - $harapan, 2),
                'label' => $kategoriLabel[$kat]
            ];
        }

        return $result;
    }

    /**
     * API Detail Jawaban
     */
    public function getDetailJawaban(int $penilaianId)
    {
        try {
            $penilaian = PenilaianFasilitas::findOrFail($penilaianId);
            $nilai = $penilaian->nilai;

            // Log untuk debugging
            Log::info('Detail Jawaban Fasilitas - ID: ' . $penilaianId);
            Log::info('Data nilai: ', ['nilai' => $nilai]);

            // Decode jika string
            $dataJawaban = is_string($nilai) ? json_decode($nilai, true) : $nilai;
            $detailJawaban = [];

            if (is_array($dataJawaban) && !empty($dataJawaban)) {
                // Cek format data
                $firstKey = array_key_first($dataJawaban);
                $firstItem = $dataJawaban[$firstKey];

                // Format 1: {"1":{"id_pertanyaan":1,"harapan":4,"persepsi":5}}
                if (is_array($firstItem) && isset($firstItem['id_pertanyaan'])) {
                    foreach ($dataJawaban as $item) {
                        if (isset($item['id_pertanyaan'])) {
                            $pertanyaan = Pertanyaan::find($item['id_pertanyaan']);
                            $harapan = $item['harapan'] ?? 0;
                            $persepsi = $item['persepsi'] ?? 0;
                            $detailJawaban[] = [
                                'no' => count($detailJawaban) + 1,
                                'pertanyaan' => $pertanyaan ? $pertanyaan->teks : 'Pertanyaan ID: ' . $item['id_pertanyaan'],
                                'kategori' => $pertanyaan ? ucfirst($pertanyaan->kategori_fasilitas ?? '-') : '-',
                                'harapan' => $harapan,
                                'persepsi' => $persepsi,
                                'gap' => $persepsi - $harapan,
                            ];
                        }
                    }
                }
                // Format 2: {"1":4, "2":5}
                elseif (is_numeric($firstItem)) {
                    foreach ($dataJawaban as $idPertanyaan => $nilaiJawaban) {
                        $pertanyaan = Pertanyaan::find($idPertanyaan);
                        $detailJawaban[] = [
                            'no' => count($detailJawaban) + 1,
                            'pertanyaan' => $pertanyaan ? $pertanyaan->teks : 'Pertanyaan ID: ' . $idPertanyaan,
                            'kategori' => $pertanyaan ? ucfirst($pertanyaan->kategori_fasilitas ?? '-') : '-',
                            'harapan' => 0,
                            'persepsi' => $nilaiJawaban,
                            'gap' => $nilaiJawaban,
                        ];
                    }
                }
                // Format 3: [{"id_pertanyaan":1,"harapan":4,"persepsi":5}]
                elseif (isset($firstItem['persepsi']) && !isset($firstItem['id_pertanyaan'])) {
                    foreach ($dataJawaban as $index => $item) {
                        if (isset($item['persepsi'])) {
                            $detailJawaban[] = [
                                'no' => count($detailJawaban) + 1,
                                'pertanyaan' => $item['teks'] ?? 'Pertanyaan ' . ($index + 1),
                                'kategori' => $item['kategori'] ?? '-',
                                'harapan' => $item['harapan'] ?? 0,
                                'persepsi' => $item['persepsi'] ?? 0,
                                'gap' => ($item['persepsi'] ?? 0) - ($item['harapan'] ?? 0),
                            ];
                        }
                    }
                }
            }

            Log::info('Jumlah jawaban: ' . count($detailJawaban));

            return response()->json([
                'success' => true,
                'data' => [
                    'mahasiswa' => $penilaian->mahasiswa_nama,
                    'nim' => $penilaian->mahasiswa_nim,
                    'rata_rata' => $penilaian->rata_rata,
                    'tanggal' => $penilaian->created_at->format('d/m/Y H:i'),
                    'jawaban' => $detailJawaban,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getDetailJawaban Fasilitas: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => [
                    'mahasiswa' => '-',
                    'nim' => '-',
                    'rata_rata' => 0,
                    'tanggal' => '-',
                    'jawaban' => [],
                ]
            ], 500);
        }
    }
}