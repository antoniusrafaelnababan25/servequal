<?php

namespace App\Exports;

use App\Models\PenilaianDosen;
use App\Models\User;
use App\Models\Pertanyaan;
use App\Models\KuesionerPeriode;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PenilaianMahasiswaExport implements WithMultipleSheets
{
    protected Request $request;
    protected int $dosenId;
    protected ?User $dosen;
    protected array $filters;

    public function __construct(Request $request, int $dosenId)
    {
        $this->request = $request;
        $this->dosenId = $dosenId;
        $this->dosen = User::find($dosenId);
        $this->filters = [
            'periode' => $this->getPeriodeName(),
            'kelas' => $this->request->kelas ?? 'Semua',
            'search' => $this->request->search ?? 'Semua',
            'start_date' => $this->request->start_date ?? 'Semua',
            'end_date' => $this->request->end_date ?? 'Semua',
        ];
    }

    private function getPeriodeName(): string
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

        // Sheet 1: Data Penilaian Mahasiswa (Ringkasan)
        $sheets[] = new DataPenilaianSheet($this->request, $this->dosenId, $this->dosen, $this->filters);

        // Sheet 2: Detail Jawaban per Penilaian
        $sheets[] = new DetailJawabanSheet($this->request, $this->dosenId, $this->dosen, $this->filters);

        // Sheet 3: Rekap Statistik
        $sheets[] = new RekapStatistikSheet($this->request, $this->dosenId, $this->dosen, $this->filters);

        // Sheet 4: Review & Kesimpulan
        $sheets[] = new ReviewSheet($this->request, $this->dosenId, $this->dosen, $this->filters);

        return $sheets;
    }
}

// ============================================================
// SHEET 1: DATA PENILAIAN MAHASISWA (RINGKASAN)
// ============================================================
class DataPenilaianSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithEvents, WithColumnFormatting
{
    protected Request $request;
    protected int $dosenId;
    protected ?User $dosen;
    protected array $filters;
    protected int $rowNumber;

    public function __construct(Request $request, int $dosenId, ?User $dosen, array $filters)
    {
        $this->request = $request;
        $this->dosenId = $dosenId;
        $this->dosen = $dosen;
        $this->filters = $filters;
        $this->rowNumber = 0;
    }

    public function collection()
    {
        $query = PenilaianDosen::where('dosen_id', $this->dosenId)->with('mahasiswa', 'periode');

        if ($this->request->filled('periode_id')) {
            $query->where('periode_id', $this->request->periode_id);
        }
        if ($this->request->filled('kelas')) {
            $query->where('kelas', $this->request->kelas);
        }
        if ($this->request->filled('search')) {
            $search = $this->request->search;
            $query->where(function ($q) use ($search) {
                $q->where('mahasiswa_nama', 'like', "%{$search}%")
                    ->orWhere('mahasiswa_nim', 'like', "%{$search}%");
            });
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
            'Nama Mahasiswa',
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
            'H' => '#,##0.00',
        ];
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $sheet = $event->getSheet()->getDelegate();
                $this->addHeader($sheet, 'DATA PENILAIAN MAHASISWA');
            },
        ];
    }

    protected function addHeader(Worksheet $sheet, string $title): void
    {
        $periodText = ($this->filters['start_date'] == 'Semua' && $this->filters['end_date'] == 'Semua')
            ? 'All Time'
            : $this->filters['start_date'] . ' – ' . $this->filters['end_date'];

        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', 'POLITEKNIK NEGERI MEDAN');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $sheet->mergeCells('A2:H2');
        $sheet->setCellValue('A2', 'SISTEM MONITORING SERVQUAL');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $sheet->mergeCells('A3:H3');
        $sheet->setCellValue('A3', $title);
        $sheet->getStyle('A3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $sheet->mergeCells('A4:H4');
        $sheet->setCellValue('A4', "Nama Dosen: {$this->dosen->name} | NIDN: {$this->dosen->nidn}");
        $sheet->getStyle('A4')->applyFromArray([
            'font' => ['size' => 10],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $sheet->mergeCells('A5:H5');
        $sheet->setCellValue('A5', "Periode Kuesioner: {$this->filters['periode']} | Kelas: {$this->filters['kelas']}");
        $sheet->getStyle('A5')->applyFromArray([
            'font' => ['size' => 9, 'italic' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $sheet->mergeCells('A6:H6');
        $sheet->setCellValue('A6', "Generated on: " . Carbon::now()->format('d/m/Y H:i:s'));
        $sheet->getStyle('A6')->applyFromArray([
            'font' => ['size' => 9],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getStyle('A7:H7')->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4c1d95']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $lastRow = $sheet->getHighestRow();
        if ($lastRow >= 7) {
            $sheet->getStyle("A7:H{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        $sheet->getStyle('H')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->freezePane('A8');
    }
}

// ============================================================
// SHEET 2: DETAIL JAWABAN PER PENILAIAN
// ============================================================
class DetailJawabanSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithEvents
{
    protected Request $request;
    protected int $dosenId;
    protected ?User $dosen;
    protected array $filters;
    protected int $rowNumber;

    public function __construct(Request $request, int $dosenId, ?User $dosen, array $filters)
    {
        $this->request = $request;
        $this->dosenId = $dosenId;
        $this->dosen = $dosen;
        $this->filters = $filters;
        $this->rowNumber = 0;
    }

    public function collection()
    {
        $query = PenilaianDosen::where('dosen_id', $this->dosenId)->with('mahasiswa', 'periode');

        if ($this->request->filled('periode_id')) {
            $query->where('periode_id', $this->request->periode_id);
        }
        if ($this->request->filled('kelas')) {
            $query->where('kelas', $this->request->kelas);
        }
        if ($this->request->filled('search')) {
            $search = $this->request->search;
            $query->where(function ($q) use ($search) {
                $q->where('mahasiswa_nama', 'like', "%{$search}%")
                    ->orWhere('mahasiswa_nim', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'No.',
            'Mahasiswa',
            'NIM',
            'Kelas',
            'Mata Kuliah',
            'Periode',
            'Dimensi',
            'Pertanyaan',
            'Harapan',
            'Persepsi',
            'Gap',
        ];
    }

    public function map($item): array
    {
        $rows = [];
        $nilai = $item->nilai;

        if (is_string($nilai)) {
            $nilai = json_decode($nilai, true);
        }

        if (is_array($nilai)) {
            $no = 1;
            foreach ($nilai as $key => $jawaban) {
                $idPertanyaan = is_array($jawaban) ? ($jawaban['id_pertanyaan'] ?? $key) : $key;
                $pertanyaan = Pertanyaan::find($idPertanyaan);
                $dimensi = $pertanyaan ? $pertanyaan->dimensi : (is_array($jawaban) ? ($jawaban['dimensi'] ?? '-') : '-');
                $teks = $pertanyaan ? $pertanyaan->teks : (is_array($jawaban) ? ($jawaban['teks'] ?? 'Pertanyaan ' . $no) : 'Pertanyaan ' . $no);

                $rows[] = [
                    $this->rowNumber + $no,
                    $item->mahasiswa_nama,
                    $item->mahasiswa_nim,
                    $item->kelas ?? '-',
                    $item->mata_kuliah ?? '-',
                    $item->periode ? $item->periode->nama_periode : '-',
                    $dimensi,
                    $teks,
                    is_array($jawaban) ? ($jawaban['harapan'] ?? 0) : 0,
                    is_array($jawaban) ? ($jawaban['persepsi'] ?? 0) : 0,
                    (is_array($jawaban) ? ($jawaban['persepsi'] ?? 0) : 0) - (is_array($jawaban) ? ($jawaban['harapan'] ?? 0) : 0),
                ];
                $no++;
            }
            $this->rowNumber += $no - 1;
        }

        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $sheet = $event->getSheet()->getDelegate();
                $this->addHeader($sheet, 'DETAIL JAWABAN PER MAHASISWA');
            },
        ];
    }

    protected function addHeader(Worksheet $sheet, string $title): void
    {
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
        $sheet->setCellValue('A4', "Nama Dosen: {$this->dosen->name} | NIDN: {$this->dosen->nidn}");
        $sheet->getStyle('A4')->applyFromArray([
            'font' => ['size' => 10],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $sheet->mergeCells('A5:K5');
        $sheet->setCellValue('A5', "Periode Kuesioner: {$this->filters['periode']} | Kelas: {$this->filters['kelas']}");
        $sheet->getStyle('A5')->applyFromArray([
            'font' => ['size' => 9, 'italic' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $sheet->mergeCells('A6:K6');
        $sheet->setCellValue('A6', "Generated on: " . Carbon::now()->format('d/m/Y H:i:s'));
        $sheet->getStyle('A6')->applyFromArray([
            'font' => ['size' => 9],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getStyle('A7:K7')->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4c1d95']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $lastRow = $sheet->getHighestRow();
        if ($lastRow >= 7) {
            $sheet->getStyle("A7:K{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        $sheet->getStyle('A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->freezePane('A8');
    }
}

// ============================================================
// SHEET 3: REKAP STATISTIK
// ============================================================
class RekapStatistikSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithEvents
{
    protected Request $request;
    protected int $dosenId;
    protected ?User $dosen;
    protected array $filters;

    public function __construct(Request $request, int $dosenId, ?User $dosen, array $filters)
    {
        $this->request = $request;
        $this->dosenId = $dosenId;
        $this->dosen = $dosen;
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = PenilaianDosen::where('dosen_id', $this->dosenId);

        if ($this->request->filled('periode_id')) {
            $query->where('periode_id', $this->request->periode_id);
        }
        if ($this->request->filled('kelas')) {
            $query->where('kelas', $this->request->kelas);
        }
        if ($this->request->filled('search')) {
            $search = $this->request->search;
            $query->where(function ($q) use ($search) {
                $q->where('mahasiswa_nama', 'like', "%{$search}%")
                    ->orWhere('mahasiswa_nim', 'like', "%{$search}%");
            });
        }

        $total = $query->count();
        $rataRata = round($query->avg('rata_rata') ?? 0, 2);
        $tertinggi = round($query->max('rata_rata') ?? 0, 2);
        $terendah = round($query->min('rata_rata') ?? 0, 2);

        $dimensi = ['Tangible', 'Reliability', 'Responsiveness', 'Assurance', 'Empathy'];
        $rataDimensi = [];
        $penilaianList = $query->get();

        foreach ($dimensi as $dim) {
            $totalPersepsi = 0;
            $totalHarapan = 0;
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
                            $totalPersepsi += (int) ($item['persepsi'] ?? 0);
                            $totalHarapan += (int) ($item['harapan'] ?? 0);
                            $count++;
                        }
                    }
                }
            }
            $rataDimensi[] = [
                'dimensi' => $dim,
                'persepsi' => $count > 0 ? round($totalPersepsi / $count, 2) : 0,
                'harapan' => $count > 0 ? round($totalHarapan / $count, 2) : 0,
                'gap' => $count > 0 ? round(($totalPersepsi - $totalHarapan) / $count, 2) : 0,
            ];
        }

        $distribusi = [
            ['range' => 'Sangat Baik (4.5 - 5.0)', 'jumlah' => $query->whereBetween('rata_rata', [4.5, 5.0])->count()],
            ['range' => 'Baik (3.5 - 4.4)', 'jumlah' => $query->whereBetween('rata_rata', [3.5, 4.49])->count()],
            ['range' => 'Cukup (2.5 - 3.4)', 'jumlah' => $query->whereBetween('rata_rata', [2.5, 3.49])->count()],
            ['range' => 'Kurang (1.5 - 2.4)', 'jumlah' => $query->whereBetween('rata_rata', [1.5, 2.49])->count()],
            ['range' => 'Sangat Kurang (1.0 - 1.4)', 'jumlah' => $query->whereBetween('rata_rata', [1.0, 1.49])->count()],
        ];

        $data = collect();

        $data->push((object) ['section' => 'STATISTIK UMUM', 'value' => '', 'extra' => '']);
        $data->push((object) ['section' => 'Total Penilaian', 'value' => $total, 'extra' => '']);
        $data->push((object) ['section' => 'Rata-rata Keseluruhan', 'value' => $rataRata . ' / 5', 'extra' => '']);
        $data->push((object) ['section' => 'Nilai Tertinggi', 'value' => $tertinggi . ' / 5', 'extra' => '']);
        $data->push((object) ['section' => 'Nilai Terendah', 'value' => $terendah . ' / 5', 'extra' => '']);
        $data->push((object) ['section' => '', 'value' => '', 'extra' => '']);

        $data->push((object) ['section' => 'RATA-RATA PER DIMENSI SERVQUAL', 'value' => '', 'extra' => '']);
        $data->push((object) ['section' => 'Dimensi', 'value' => 'Persepsi', 'extra' => 'Harapan', 'gap' => 'Gap']);
        foreach ($rataDimensi as $rd) {
            $data->push((object) [
                'section' => $rd['dimensi'],
                'value' => $rd['persepsi'],
                'extra' => $rd['harapan'],
                'gap' => $rd['gap']
            ]);
        }
        $data->push((object) ['section' => '', 'value' => '', 'extra' => '']);

        $data->push((object) ['section' => 'DISTRIBUSI NILAI', 'value' => '', 'extra' => '']);
        $data->push((object) ['section' => 'Kategori', 'value' => 'Jumlah', 'extra' => '']);
        foreach ($distribusi as $d) {
            $data->push((object) ['section' => $d['range'], 'value' => $d['jumlah'], 'extra' => '']);
        }

        return $data;
    }

    public function headings(): array
    {
        return ['Keterangan', 'Nilai 1', 'Nilai 2', 'Nilai 3'];
    }

    public function map($item): array
    {
        if (isset($item->gap)) {
            return [$item->section, $item->value, $item->extra, $item->gap];
        }
        if (isset($item->extra) && $item->extra === 'Harapan') {
            return [$item->section, $item->value, $item->extra, ''];
        }
        return [$item->section, $item->value, $item->extra ?? '', ''];
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $sheet = $event->getSheet()->getDelegate();
                $this->addHeader($sheet, 'REKAP STATISTIK PENILAIAN');
            },
        ];
    }

    protected function addHeader(Worksheet $sheet, string $title): void
    {
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
        $sheet->setCellValue('A4', "Nama Dosen: {$this->dosen->name} | NIDN: {$this->dosen->nidn}");
        $sheet->getStyle('A4')->applyFromArray([
            'font' => ['size' => 10],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $sheet->mergeCells('A5:D5');
        $sheet->setCellValue('A5', "Periode Kuesioner: {$this->filters['periode']} | Kelas: {$this->filters['kelas']}");
        $sheet->getStyle('A5')->applyFromArray([
            'font' => ['size' => 9, 'italic' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $sheet->mergeCells('A6:D6');
        $sheet->setCellValue('A6', "Generated on: " . Carbon::now()->format('d/m/Y H:i:s'));
        $sheet->getStyle('A6')->applyFromArray([
            'font' => ['size' => 9],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getStyle('A7:D7')->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4c1d95']],
        ]);

        $sheet->getStyle('A8')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'e9ecef']],
        ]);

        $sheet->getStyle('A14')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'e9ecef']],
        ]);

        $sheet->getStyle('A21')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'e9ecef']],
        ]);

        $lastRow = $sheet->getHighestRow();
        if ($lastRow >= 7) {
            $sheet->getStyle("A7:D{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        $sheet->getStyle('B')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->freezePane('A8');
    }
}

// ============================================================
// SHEET 4: REVIEW & KESIMPULAN
// ============================================================
class ReviewSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithEvents
{
    protected Request $request;
    protected int $dosenId;
    protected ?User $dosen;
    protected array $filters;

    public function __construct(Request $request, int $dosenId, ?User $dosen, array $filters)
    {
        $this->request = $request;
        $this->dosenId = $dosenId;
        $this->dosen = $dosen;
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = PenilaianDosen::where('dosen_id', $this->dosenId);

        if ($this->request->filled('periode_id')) {
            $query->where('periode_id', $this->request->periode_id);
        }
        if ($this->request->filled('kelas')) {
            $query->where('kelas', $this->request->kelas);
        }
        if ($this->request->filled('search')) {
            $search = $this->request->search;
            $query->where(function ($q) use ($search) {
                $q->where('mahasiswa_nama', 'like', "%{$search}%")
                    ->orWhere('mahasiswa_nim', 'like', "%{$search}%");
            });
        }

        $total = $query->count();
        $rataRata = round($query->avg('rata_rata') ?? 0, 2);

        $dimensi = ['Tangible', 'Reliability', 'Responsiveness', 'Assurance', 'Empathy'];
        $gaps = [];
        $penilaianList = $query->get();
        $dimensiLabels = [
            'Tangible' => 'Fasilitas Fisik',
            'Reliability' => 'Keandalan',
            'Responsiveness' => 'Daya Tanggap',
            'Assurance' => 'Jaminan',
            'Empathy' => 'Empati'
        ];

        foreach ($dimensi as $dim) {
            $totalPersepsi = 0;
            $totalHarapan = 0;
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
                            $totalPersepsi += (int) ($item['persepsi'] ?? 0);
                            $totalHarapan += (int) ($item['harapan'] ?? 0);
                            $count++;
                        }
                    }
                }
            }
            $gap = $count > 0 ? round(($totalPersepsi - $totalHarapan) / $count, 2) : 0;
            $gaps[] = [
                'dimensi' => $dim,
                'label' => $dimensiLabels[$dim],
                'gap' => $gap,
                'kategori' => $gap >= 0 ? 'Positif (Memenuhi Harapan)' : 'Negatif (Perlu Perbaikan)',
                'rekomendasi' => $gap >= 0
                    ? 'Pertahankan kualitas layanan yang sudah baik ini.'
                    : 'Perlu peningkatan kualitas layanan pada dimensi ini.',
            ];
        }

        $kesimpulan = '';
        if ($rataRata >= 4.5) {
            $kesimpulan = 'Sangat Baik. Dosen memiliki kinerja yang sangat memuaskan. Pertahankan!';
        } elseif ($rataRata >= 3.5) {
            $kesimpulan = 'Baik. Dosen memiliki kinerja yang baik. Terus tingkatkan!';
        } elseif ($rataRata >= 2.5) {
            $kesimpulan = 'Cukup. Masih perlu peningkatan di beberapa aspek.';
        } elseif ($rataRata >= 1.5) {
            $kesimpulan = 'Kurang. Perlu perhatian serius untuk meningkatkan kualitas.';
        } else {
            $kesimpulan = 'Sangat Kurang. Diperlukan evaluasi dan perbaikan menyeluruh.';
        }

        $data = collect();

        $data->push((object) ['section' => 'KESIMPULAN UMUM', 'value' => '']);
        $data->push((object) ['section' => 'Total Penilaian', 'value' => $total . ' mahasiswa']);
        $data->push((object) ['section' => 'Rata-rata Kepuasan', 'value' => $rataRata . ' / 5.00']);
        $data->push((object) ['section' => 'Kesimpulan', 'value' => $kesimpulan]);
        $data->push((object) ['section' => '', 'value' => '']);

        $data->push((object) ['section' => 'ANALISIS GAP PER DIMENSI', 'value' => '']);
        $data->push((object) ['section' => 'Dimensi', 'value' => 'Gap', 'keterangan' => 'Kategori', 'rekomendasi' => 'Rekomendasi']);
        foreach ($gaps as $g) {
            $data->push((object) [
                'section' => $g['label'],
                'value' => $g['gap'],
                'keterangan' => $g['kategori'],
                'rekomendasi' => $g['rekomendasi']
            ]);
        }

        $data->push((object) ['section' => '', 'value' => '']);

        $data->push((object) ['section' => 'SARAN DAN REKOMENDASI', 'value' => '']);
        $negatifGaps = array_filter($gaps, function ($g) {
            return $g['gap'] < 0; });

        if (count($negatifGaps) > 0) {
            $data->push((object) ['section' => 'Prioritas Peningkatan:', 'value' => '']);
            foreach ($negatifGaps as $g) {
                $data->push((object) ['section' => '• ' . $g['label'], 'value' => "Gap: {$g['gap']} - Perlu perbaikan"]);
            }
        } else {
            $data->push((object) ['section' => 'Semua dimensi memiliki gap positif/0.', 'value' => 'Pertahankan kualitas yang sudah baik!']);
        }

        $data->push((object) ['section' => '', 'value' => '']);
        $data->push((object) ['section' => 'Kesimpulan Akhir:', 'value' => '']);
        $data->push((object) ['section' => $kesimpulan, 'value' => '']);

        return $data;
    }

    public function headings(): array
    {
        return ['Keterangan', 'Nilai', 'Kategori', 'Rekomendasi'];
    }

    public function map($item): array
    {
        if (isset($item->keterangan) && isset($item->rekomendasi)) {
            return [$item->section, $item->value, $item->keterangan, $item->rekomendasi];
        }
        return [$item->section, $item->value ?? '', '', ''];
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $sheet = $event->getSheet()->getDelegate();
                $this->addHeader($sheet, 'REVIEW & KESIMPULAN');
            },
        ];
    }

    protected function addHeader(Worksheet $sheet, string $title): void
    {
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
        $sheet->setCellValue('A4', "Nama Dosen: {$this->dosen->name} | NIDN: {$this->dosen->nidn}");
        $sheet->getStyle('A4')->applyFromArray([
            'font' => ['size' => 10],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $sheet->mergeCells('A5:D5');
        $sheet->setCellValue('A5', "Periode Kuesioner: {$this->filters['periode']} | Kelas: {$this->filters['kelas']}");
        $sheet->getStyle('A5')->applyFromArray([
            'font' => ['size' => 9, 'italic' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $sheet->mergeCells('A6:D6');
        $sheet->setCellValue('A6', "Generated on: " . Carbon::now()->format('d/m/Y H:i:s'));
        $sheet->getStyle('A6')->applyFromArray([
            'font' => ['size' => 9],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getStyle('A7:D7')->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4c1d95']],
        ]);

        $sheet->getStyle('A8')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'e9ecef']],
        ]);

        $sheet->getStyle('A13')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'e9ecef']],
        ]);

        $sheet->getStyle('A14:D14')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'f8f9fa']],
        ]);

        $lastRow = $sheet->getHighestRow();
        if ($lastRow >= 7) {
            $sheet->getStyle("A7:D{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        $sheet->getStyle('B')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->freezePane('A8');
    }
}