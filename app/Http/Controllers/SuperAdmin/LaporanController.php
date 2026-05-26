<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\PenilaianDosen;
use App\Models\PenilaianFasilitas;
use App\Models\User;
use App\Models\Pertanyaan;
use App\Models\Jurusan;
use App\Models\Prodi;
use App\Models\KuesionerPeriode;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanExport;
use App\Exports\LaporanFasilitasExport;

class LaporanController extends Controller
{
    /**
     * Halaman laporan penilaian dosen
     */
    public function index(Request $request)
    {
        $periodeId = $request->input('periode_id');
        $periodeTerpilih = $periodeId ? KuesionerPeriode::find($periodeId) : null;

        $query = PenilaianDosen::with(['dosen', 'mahasiswa']);
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
                    $q->where('jurusan', $jurusan->nama_jurusan);
                });
            }
        }

        // Filter tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $data = $query->orderBy('created_at', 'desc')->paginate(20);

        $dosenList = User::where('role', 'dosen')->orderBy('name')->get();
        $jurusanList = Jurusan::orderBy('nama_jurusan')->get();
        $periodeList = KuesionerPeriode::orderBy('created_at', 'desc')->get();

        return view('superadmin.laporan.index', compact('data', 'dosenList', 'jurusanList', 'periodeList', 'periodeTerpilih'));
    }

    /**
     * Halaman laporan penilaian fasilitas
     */
    public function fasilitas(Request $request)
    {
        $periodeId = $request->input('periode_id');
        $periodeTerpilih = $periodeId ? KuesionerPeriode::find($periodeId) : null;

        $query = PenilaianFasilitas::with('mahasiswa');
        if ($periodeTerpilih) {
            $query->where('periode_id', $periodeTerpilih->id);
        }

        // Filter berdasarkan mahasiswa
        if ($request->filled('mahasiswa_id')) {
            $query->where('mahasiswa_id', $request->mahasiswa_id);
        }

        // Filter berdasarkan jurusan
        if ($request->filled('jurusan_id')) {
            $jurusan = Jurusan::find($request->jurusan_id);
            if ($jurusan) {
                $query->whereHas('mahasiswa', function ($q) use ($jurusan) {
                    $q->where('jurusan', $jurusan->nama_jurusan);
                });
            }
        }

        // Filter tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $data = $query->orderBy('created_at', 'desc')->paginate(20);

        $mahasiswaList = User::where('role', 'mahasiswa')->orderBy('name')->get();
        $jurusanList = Jurusan::orderBy('nama_jurusan')->get();
        $periodeList = KuesionerPeriode::orderBy('created_at', 'desc')->get();

        return view('superadmin.laporan.fasilitas', compact('data', 'mahasiswaList', 'jurusanList', 'periodeList', 'periodeTerpilih'));
    }

    /**
     * Export laporan penilaian dosen ke Excel
     */
    public function exportExcel(Request $request)
    {
        return Excel::download(new LaporanExport($request), 'laporan_penilaian_dosen.xlsx');
    }

    /**
     * Export laporan penilaian fasilitas ke Excel
     */
    public function exportExcelFasilitas(Request $request)
    {
        return Excel::download(new LaporanFasilitasExport($request), 'laporan_penilaian_fasilitas.xlsx');
    }

    /**
     * Detail penilaian untuk satu dosen
     */
    public function detailDosen($dosenId)
    {
        $dosen = User::findOrFail($dosenId);
        $penilaian = PenilaianDosen::where('dosen_id', $dosenId)->orderBy('created_at', 'desc')->paginate(15);

        $statistik = [
            'total' => PenilaianDosen::where('dosen_id', $dosenId)->count(),
            'rata_rata' => round(PenilaianDosen::where('dosen_id', $dosenId)->avg('rata_rata') ?? 0, 2),
            'tertinggi' => round(PenilaianDosen::where('dosen_id', $dosenId)->max('rata_rata') ?? 0, 2),
            'terendah' => round(PenilaianDosen::where('dosen_id', $dosenId)->min('rata_rata') ?? 0, 2),
        ];

        $chartData = $this->getChartDataForDosen($dosenId);

        return view('superadmin.laporan.detail_dosen', compact('dosen', 'penilaian', 'statistik', 'chartData'));
    }

    /**
     * Detail penilaian untuk satu mahasiswa (fasilitas)
     */
    public function detailMahasiswa($mahasiswaId)
    {
        $mahasiswa = User::findOrFail($mahasiswaId);
        $penilaian = PenilaianFasilitas::where('mahasiswa_id', $mahasiswaId)->orderBy('created_at', 'desc')->paginate(15);

        $statistik = [
            'total' => PenilaianFasilitas::where('mahasiswa_id', $mahasiswaId)->count(),
            'rata_rata' => round(PenilaianFasilitas::where('mahasiswa_id', $mahasiswaId)->avg('rata_rata') ?? 0, 2),
            'tertinggi' => round(PenilaianFasilitas::where('mahasiswa_id', $mahasiswaId)->max('rata_rata') ?? 0, 2),
            'terendah' => round(PenilaianFasilitas::where('mahasiswa_id', $mahasiswaId)->min('rata_rata') ?? 0, 2),
        ];

        $chartData = $this->getChartDataForMahasiswa($mahasiswaId);

        return view('superadmin.laporan.detail_mahasiswa', compact('mahasiswa', 'penilaian', 'statistik', 'chartData'));
    }

    /**
     * API: Detail jawaban penilaian dosen
     */
    public function getDetailJawaban($penilaianId)
    {
        $penilaian = PenilaianDosen::findOrFail($penilaianId);
        $nilai = json_decode($penilaian->nilai, true);
        $detailJawaban = [];

        if (is_array($nilai)) {
            foreach ($nilai as $item) {
                if (isset($item['id_pertanyaan'])) {
                    $pertanyaan = Pertanyaan::find($item['id_pertanyaan']);
                    $detailJawaban[] = [
                        'pertanyaan' => $pertanyaan ? $pertanyaan->teks : '-',
                        'dimensi' => $pertanyaan ? $pertanyaan->dimensi : '-',
                        'harapan' => $item['harapan'] ?? 0,
                        'persepsi' => $item['persepsi'] ?? 0,
                        'gap' => ($item['persepsi'] ?? 0) - ($item['harapan'] ?? 0),
                    ];
                }
            }
        }

        return response()->json(['success' => true, 'data' => ['jawaban' => $detailJawaban]]);
    }

    /**
     * API: Detail jawaban penilaian fasilitas
     */
    public function getDetailJawabanFasilitas($penilaianId)
    {
        $penilaian = PenilaianFasilitas::findOrFail($penilaianId);
        $nilai = json_decode($penilaian->nilai, true);
        $detailJawaban = [];

        if (is_array($nilai)) {
            foreach ($nilai as $item) {
                if (isset($item['id_pertanyaan'])) {
                    $pertanyaan = Pertanyaan::find($item['id_pertanyaan']);
                    $detailJawaban[] = [
                        'pertanyaan' => $pertanyaan ? $pertanyaan->teks : '-',
                        'kategori' => $pertanyaan ? $pertanyaan->kategori_fasilitas : '-',
                        'harapan' => $item['harapan'] ?? 0,
                        'persepsi' => $item['persepsi'] ?? 0,
                        'gap' => ($item['persepsi'] ?? 0) - ($item['harapan'] ?? 0),
                    ];
                }
            }
        }

        return response()->json(['success' => true, 'data' => ['jawaban' => $detailJawaban]]);
    }

    /**
     * Chart data untuk satu dosen
     */
    private function getChartDataForDosen($dosenId)
    {
        $dimensi = ['Tangible', 'Reliability', 'Responsiveness', 'Assurance', 'Empathy'];
        $result = [];

        $penilaianList = PenilaianDosen::where('dosen_id', $dosenId)->get();

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

    /**
     * Chart data untuk satu mahasiswa (fasilitas)
     */
    private function getChartDataForMahasiswa($mahasiswaId)
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
                $result[$kat] = ['persepsi' => 0, 'harapan' => 0, 'gap' => 0, 'label' => $kategoriLabel[$kat]];
            }
            return $result;
        }

        $totalPersepsi = array_fill_keys($kategori, 0);
        $totalHarapan = array_fill_keys($kategori, 0);
        $count = array_fill_keys($kategori, 0);

        foreach ($penilaianList as $penilaian) {
            $nilai = json_decode($penilaian->nilai, true);
            if (is_array($nilai)) {
                foreach ($nilai as $item) {
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
            $result[$kat] = ['persepsi' => $persepsi, 'harapan' => $harapan, 'gap' => round($persepsi - $harapan, 2), 'label' => $kategoriLabel[$kat]];
        }

        return $result;
    }
}