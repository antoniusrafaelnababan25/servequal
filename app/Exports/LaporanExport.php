<?php

namespace App\Exports;

use App\Models\PenilaianDosen;
use App\Models\User;
use App\Models\Jurusan;
use App\Models\KuesionerPeriode;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanExport implements WithMultipleSheets
{
    protected $request;
    protected $filters;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->filters = [
            'dosen' => $this->getDosenName(),
            'jurusan' => $this->getJurusanName(),
            'periode' => $this->getPeriodeName(),
            'start_date' => $this->request->start_date ?? 'Semua',
            'end_date' => $this->request->end_date ?? 'Semua',
        ];
    }

    private function getDosenName()
    {
        if ($this->request->filled('dosen_id')) {
            $dosen = User::find($this->request->dosen_id);
            return $dosen ? $dosen->name : 'Semua Dosen';
        }
        return 'Semua Dosen';
    }

    private function getJurusanName()
    {
        if ($this->request->filled('jurusan_id')) {
            $jurusan = Jurusan::find($this->request->jurusan_id);
            return $jurusan ? $jurusan->nama_jurusan : 'Semua Jurusan';
        }
        return 'Semua Jurusan';
    }

    private function getPeriodeName()
    {
        if ($this->request->filled('periode_id')) {
            $periode = KuesionerPeriode::find($this->request->periode_id);
            return $periode ? $periode->nama_periode : 'Semua Periode';
        }
        return 'Semua Periode';
    }

    public function sheets(): array
    {
        $sheets = [];

        // Sheet 1: Data Penilaian Dosen (tanpa kolom JSON)
        $sheets[] = new PenilaianDosenSheet($this->request, $this->filters);

        // Sheet 2: Rekap per Dosen
        $sheets[] = new RekapDosenSheet($this->request, $this->filters);

        // Sheet 3: Rekap per Jurusan
        $sheets[] = new RekapJurusanSheet($this->request, $this->filters);

        // Sheet 4: Review / Kesimpulan
        $sheets[] = new ReviewSheet($this->request, $this->filters);

        return $sheets;
    }
}

// ============================================================
// SHEET 1: DATA PENILAIAN DOSEN (TAMBAH KOLOM PERIODE)
// ============================================================
class PenilaianDosenSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnFormatting, ShouldAutoSize, WithEvents
{
    protected $request;
    protected $filters;
    protected $rowNumber;

    public function __construct(Request $request, array $filters)
    {
        $this->request = $request;
        $this->filters = $filters;
        $this->rowNumber = 0;
    }

    public function collection()
    {
        $query = PenilaianDosen::with(['dosen', 'mahasiswa', 'periode']);

        // Filter berdasarkan periode
        if ($this->request->filled('periode_id')) {
            $query->where('periode_id', $this->request->periode_id);
        }

        if ($this->request->filled('dosen_id')) {
            $query->where('dosen_id', $this->request->dosen_id);
        }
        if ($this->request->filled('jurusan_id')) {
            $jurusan = Jurusan::find($this->request->jurusan_id);
            if ($jurusan) {
                $query->whereHas('dosen', function ($q) use ($jurusan) {
                    $q->where('jurusan', $jurusan->nama_jurusan);
                });
            }
        }
        if ($this->request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $this->request->start_date);
        }
        if ($this->request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $this->request->end_date);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'No.',
            'Tanggal Penilaian',
            'Periode',
            'Dosen',
            'NIDN',
            'Jurusan Dosen',
            'Mahasiswa',
            'NIM',
            'Kelas',
            'Mata Kuliah',
            'Rata-rata',
        ];
    }

    public function map($item): array
    {
        $this->rowNumber++;
        return [
            $this->rowNumber,
            Date::dateTimeToExcel($item->created_at),
            $item->periode ? $item->periode->nama_periode : '-',
            $item->dosen_nama,
            $item->dosen->nidn ?? '-',
            $item->dosen->jurusan ?? '-',
            $item->mahasiswa_nama,
            $item->mahasiswa_nim,
            $item->kelas ?? '-',
            $item->mata_kuliah ?? '-',
            $item->rata_rata ?? 0,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => '0',
            'B' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'K' => '#,##0.00',
        ];
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $sheet = $event->getSheet()->getDelegate();
                $this->addHeader($sheet, 'DATA PENILAIAN DOSEN');
            },
        ];
    }

    protected function addHeader($sheet, $title)
    {
        $periodText = ($this->filters['start_date'] == 'Semua' && $this->filters['end_date'] == 'Semua')
            ? 'All Time'
            : $this->filters['start_date'] . ' – ' . $this->filters['end_date'];

        $sheet->mergeCells('A1:K1');
        $sheet->setCellValue('A1', 'POLITEKNIK NEGERI MEDAN');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $sheet->mergeCells('A2:K2');
        $sheet->setCellValue('A2', 'SISTEM MONITORING SERVQUAL');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $sheet->mergeCells('A3:K3');
        $sheet->setCellValue('A3', $title);
        $sheet->getStyle('A3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $sheet->mergeCells('A4:K4');
        $sheet->setCellValue('A4', "Periode Kuesioner: {$this->filters['periode']} | Periode Tanggal: {$periodText} | Dosen: {$this->filters['dosen']} | Jurusan: {$this->filters['jurusan']}");
        $sheet->getStyle('A4')->applyFromArray([
            'font' => ['size' => 9, 'italic' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $sheet->mergeCells('A5:K5');
        $sheet->setCellValue('A5', 'Generated on: ' . Carbon::now()->format('d/m/Y H:i:s'));
        $sheet->getStyle('A5')->applyFromArray([
            'font' => ['size' => 9],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A6:K6')->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4c1d95']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $lastRow = $sheet->getHighestRow();
        if ($lastRow >= 6) {
            $sheet->getStyle("A6:K{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        $sheet->getStyle('K')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->freezePane('A7');
    }
}

// ============================================================
// SHEET 2: REKAP PER DOSEN
// ============================================================
class RekapDosenSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithEvents
{
    protected $request;
    protected $filters;
    protected $rowNumber;

    public function __construct(Request $request, array $filters)
    {
        $this->request = $request;
        $this->filters = $filters;
        $this->rowNumber = 0;
    }

    public function collection()
    {
        $dosenList = User::where('role', 'dosen')->get();
        $rekap = [];

        foreach ($dosenList as $dosen) {
            $query = PenilaianDosen::where('dosen_id', $dosen->id);

            // Filter berdasarkan periode
            if ($this->request->filled('periode_id')) {
                $query->where('periode_id', $this->request->periode_id);
            }
            if ($this->request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $this->request->start_date);
            }
            if ($this->request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $this->request->end_date);
            }

            $total = $query->count();
            $rataRata = $query->avg('rata_rata') ?? 0;

            if ($total > 0) {
                $rekap[] = (object) [
                    'dosen_id' => $dosen->id,
                    'dosen_nama' => $dosen->name,
                    'nidn' => $dosen->nidn ?? '-',
                    'jurusan' => $dosen->jurusan ?? '-',
                    'total_penilaian' => $total,
                    'rata_rata' => round($rataRata, 2),
                ];
            }
        }

        usort($rekap, function ($a, $b) {
            return $b->rata_rata <=> $a->rata_rata;
        });

        return collect($rekap);
    }

    public function headings(): array
    {
        return [
            'No.',
            'Nama Dosen',
            'NIDN',
            'Jurusan',
            'Total Penilaian',
            'Rata-rata',
        ];
    }

    public function map($item): array
    {
        $this->rowNumber++;
        return [
            $this->rowNumber,
            $item->dosen_nama,
            $item->nidn,
            $item->jurusan,
            $item->total_penilaian,
            $item->rata_rata,
        ];
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $sheet = $event->getSheet()->getDelegate();
                $this->addHeader($sheet, 'REKAP PENILAIAN PER DOSEN');
            },
        ];
    }

    protected function addHeader($sheet, $title)
    {
        $periodText = ($this->filters['start_date'] == 'Semua' && $this->filters['end_date'] == 'Semua')
            ? 'All Time'
            : $this->filters['start_date'] . ' – ' . $this->filters['end_date'];

        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', 'POLITEKNIK NEGERI MEDAN');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $sheet->mergeCells('A2:F2');
        $sheet->setCellValue('A2', 'SISTEM MONITORING SERVQUAL');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $sheet->mergeCells('A3:F3');
        $sheet->setCellValue('A3', $title);
        $sheet->getStyle('A3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $sheet->mergeCells('A4:F4');
        $sheet->setCellValue('A4', "Periode Kuesioner: {$this->filters['periode']} | Periode Tanggal: {$periodText}");
        $sheet->getStyle('A4')->applyFromArray([
            'font' => ['size' => 9, 'italic' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $sheet->mergeCells('A5:F5');
        $sheet->setCellValue('A5', 'Generated on: ' . Carbon::now()->format('d/m/Y H:i:s'));
        $sheet->getStyle('A5')->applyFromArray([
            'font' => ['size' => 9],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A6:F6')->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4c1d95']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $lastRow = $sheet->getHighestRow();
        if ($lastRow >= 6) {
            $sheet->getStyle("A6:F{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        $sheet->getStyle('F')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->freezePane('A7');
    }
}

// ============================================================
// SHEET 3: REKAP PER JURUSAN
// ============================================================
class RekapJurusanSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithEvents
{
    protected $request;
    protected $filters;
    protected $rowNumber;

    public function __construct(Request $request, array $filters)
    {
        $this->request = $request;
        $this->filters = $filters;
        $this->rowNumber = 0;
    }

    public function collection()
    {
        $jurusanList = Jurusan::all();
        $rekap = [];

        foreach ($jurusanList as $jurusan) {
            $query = PenilaianDosen::whereHas('dosen', function ($q) use ($jurusan) {
                $q->where('jurusan', $jurusan->nama_jurusan);
            });

            // Filter berdasarkan periode
            if ($this->request->filled('periode_id')) {
                $query->where('periode_id', $this->request->periode_id);
            }
            if ($this->request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $this->request->start_date);
            }
            if ($this->request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $this->request->end_date);
            }

            $total = $query->count();
            $rataRata = $query->avg('rata_rata') ?? 0;

            if ($total > 0) {
                $rekap[] = (object) [
                    'jurusan' => $jurusan->nama_jurusan,
                    'total_penilaian' => $total,
                    'rata_rata' => round($rataRata, 2),
                ];
            }
        }

        usort($rekap, function ($a, $b) {
            return $b->rata_rata <=> $a->rata_rata;
        });

        return collect($rekap);
    }

    public function headings(): array
    {
        return [
            'No.',
            'Jurusan',
            'Total Penilaian',
            'Rata-rata',
        ];
    }

    public function map($item): array
    {
        $this->rowNumber++;
        return [
            $this->rowNumber,
            $item->jurusan,
            $item->total_penilaian,
            $item->rata_rata,
        ];
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $sheet = $event->getSheet()->getDelegate();
                $this->addHeader($sheet, 'REKAP PENILAIAN PER JURUSAN');
            },
        ];
    }

    protected function addHeader($sheet, $title)
    {
        $periodText = ($this->filters['start_date'] == 'Semua' && $this->filters['end_date'] == 'Semua')
            ? 'All Time'
            : $this->filters['start_date'] . ' – ' . $this->filters['end_date'];

        $sheet->mergeCells('A1:D1');
        $sheet->setCellValue('A1', 'POLITEKNIK NEGERI MEDAN');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $sheet->mergeCells('A2:D2');
        $sheet->setCellValue('A2', 'SISTEM MONITORING SERVQUAL');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $sheet->mergeCells('A3:D3');
        $sheet->setCellValue('A3', $title);
        $sheet->getStyle('A3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $sheet->mergeCells('A4:D4');
        $sheet->setCellValue('A4', "Periode Kuesioner: {$this->filters['periode']} | Periode Tanggal: {$periodText}");
        $sheet->getStyle('A4')->applyFromArray([
            'font' => ['size' => 9, 'italic' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $sheet->mergeCells('A5:D5');
        $sheet->setCellValue('A5', 'Generated on: ' . Carbon::now()->format('d/m/Y H:i:s'));
        $sheet->getStyle('A5')->applyFromArray([
            'font' => ['size' => 9],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A6:D6')->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4c1d95']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $lastRow = $sheet->getHighestRow();
        if ($lastRow >= 6) {
            $sheet->getStyle("A6:D{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        $sheet->getStyle('D')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->freezePane('A7');
    }
}

// ============================================================
// SHEET 4: REVIEW / KESIMPULAN
// ============================================================
class ReviewSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithEvents
{
    protected $request;
    protected $filters;
    protected $rowNumber;

    public function __construct(Request $request, array $filters)
    {
        $this->request = $request;
        $this->filters = $filters;
        $this->rowNumber = 0;
    }

    public function collection()
    {
        $query = PenilaianDosen::query();

        // Filter berdasarkan periode
        if ($this->request->filled('periode_id')) {
            $query->where('periode_id', $this->request->periode_id);
        }
        if ($this->request->filled('dosen_id')) {
            $query->where('dosen_id', $this->request->dosen_id);
        }
        if ($this->request->filled('jurusan_id')) {
            $jurusan = Jurusan::find($this->request->jurusan_id);
            if ($jurusan) {
                $query->whereHas('dosen', function ($q) use ($jurusan) {
                    $q->where('jurusan', $jurusan->nama_jurusan);
                });
            }
        }
        if ($this->request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $this->request->start_date);
        }
        if ($this->request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $this->request->end_date);
        }

        $total = $query->count();
        $rataRata = round($query->avg('rata_rata') ?? 0, 2);
        $tertinggi = round($query->max('rata_rata') ?? 0, 2);
        $terendah = round($query->min('rata_rata') ?? 0, 2);

        $dimensi = ['Tangible', 'Reliability', 'Responsiveness', 'Assurance', 'Empathy'];
        $gaps = [];
        $penilaianList = $query->get();

        foreach ($dimensi as $dim) {
            $persepsi = 0;
            $harapan = 0;
            $count = 0;

            foreach ($penilaianList as $penilaian) {
                $nilai = $penilaian->nilai;
                if (is_string($nilai)) {
                    $nilai = json_decode($nilai, true);
                }
                if (is_array($nilai)) {
                    foreach ($nilai as $item) {
                        $dimensiItem = $item['dimensi'] ?? null;
                        if ($dimensiItem == $dim) {
                            $persepsi += (int) ($item['persepsi'] ?? 0);
                            $harapan += (int) ($item['harapan'] ?? 0);
                            $count++;
                        }
                    }
                }
            }
            $avgPersepsi = $count > 0 ? round($persepsi / $count, 2) : 0;
            $avgHarapan = $count > 0 ? round($harapan / $count, 2) : 0;
            $gaps[] = (object) [
                'dimensi' => $dim,
                'persepsi' => $avgPersepsi,
                'harapan' => $avgHarapan,
                'gap' => round($avgPersepsi - $avgHarapan, 2),
            ];
        }

        $topDosen = [];
        $dosenList = User::where('role', 'dosen')->get();
        foreach ($dosenList as $dosen) {
            $dosenQuery = PenilaianDosen::where('dosen_id', $dosen->id);
            if ($this->request->filled('periode_id')) {
                $dosenQuery->where('periode_id', $this->request->periode_id);
            }
            if ($this->request->filled('start_date')) {
                $dosenQuery->whereDate('created_at', '>=', $this->request->start_date);
            }
            if ($this->request->filled('end_date')) {
                $dosenQuery->whereDate('created_at', '<=', $this->request->end_date);
            }
            $rata = $dosenQuery->avg('rata_rata') ?? 0;
            if ($rata > 0) {
                $topDosen[] = (object) [
                    'nama' => $dosen->name,
                    'rata_rata' => round($rata, 2),
                ];
            }
        }
        usort($topDosen, function ($a, $b) {
            return $b->rata_rata <=> $a->rata_rata;
        });
        $topDosen = array_slice($topDosen, 0, 5);

        return collect([
            (object) ['section' => 'STATISTIK UMUM', 'value' => ''],
            (object) ['section' => 'Total Penilaian', 'value' => $total],
            (object) ['section' => 'Rata-rata Kepuasan', 'value' => $rataRata . ' / 5'],
            (object) ['section' => 'Nilai Tertinggi', 'value' => $tertinggi . ' / 5'],
            (object) ['section' => 'Nilai Terendah', 'value' => $terendah . ' / 5'],
            (object) ['section' => ''],
            (object) ['section' => 'GAP PER DIMENSI SERVQUAL', 'value' => ''],
            (object) ['section' => 'Dimensi', 'value' => 'Gap'],
            ...$gaps,
            (object) ['section' => ''],
            (object) ['section' => 'TOP 5 DOSEN TERBAIK', 'value' => ''],
            (object) ['section' => 'Nama Dosen', 'value' => 'Rata-rata'],
            ...$topDosen,
        ]);
    }

    public function headings(): array
    {
        return ['Keterangan', 'Nilai'];
    }

    public function map($item): array
    {
        if (isset($item->dimensi)) {
            return [$item->dimensi, $item->gap];
        }
        if (isset($item->nama)) {
            return [$item->nama, $item->rata_rata];
        }
        return [$item->section, $item->value ?? ''];
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $sheet = $event->getSheet()->getDelegate();
                $this->addHeader($sheet);
            },
        ];
    }

    protected function addHeader($sheet)
    {
        $periodText = ($this->filters['start_date'] == 'Semua' && $this->filters['end_date'] == 'Semua')
            ? 'All Time'
            : $this->filters['start_date'] . ' – ' . $this->filters['end_date'];

        $sheet->mergeCells('A1:B1');
        $sheet->setCellValue('A1', 'POLITEKNIK NEGERI MEDAN');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $sheet->mergeCells('A2:B2');
        $sheet->setCellValue('A2', 'SISTEM MONITORING SERVQUAL');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $sheet->mergeCells('A3:B3');
        $sheet->setCellValue('A3', 'REVIEW & KESIMPULAN');
        $sheet->getStyle('A3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $sheet->mergeCells('A4:B4');
        $sheet->setCellValue('A4', "Periode Kuesioner: {$this->filters['periode']} | Periode Tanggal: {$periodText} | Dosen: {$this->filters['dosen']} | Jurusan: {$this->filters['jurusan']}");
        $sheet->getStyle('A4')->applyFromArray([
            'font' => ['size' => 9, 'italic' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $sheet->mergeCells('A5:B5');
        $sheet->setCellValue('A5', 'Generated on: ' . Carbon::now()->format('d/m/Y H:i:s'));
        $sheet->getStyle('A5')->applyFromArray([
            'font' => ['size' => 9],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A6:B6')->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4c1d95']],
        ]);

        $sheet->getStyle('A7:B7')->applyFromArray([
            'font' => ['bold' => true, 'size' => 9],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'e9ecef']],
        ]);

        $sheet->getStyle('A13:B13')->applyFromArray([
            'font' => ['bold' => true, 'size' => 9],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'e9ecef']],
        ]);

        $lastRow = $sheet->getHighestRow();
        if ($lastRow >= 6) {
            $sheet->getStyle("A6:B{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        $sheet->getStyle('B')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->freezePane('A7');
    }
}