<?php

namespace App\Exports;

use App\Models\PenilaianFasilitas;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Http\Request;

class LaporanFasilitasExport implements FromQuery, WithHeadings, WithMapping
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $query = PenilaianFasilitas::with('mahasiswa');

        if ($this->request->filled('periode_id')) {
            $query->where('periode_id', $this->request->periode_id);
        }
        if ($this->request->filled('mahasiswa_id')) {
            $query->where('mahasiswa_id', $this->request->mahasiswa_id);
        }
        if ($this->request->filled('jurusan_id')) {
            $jurusan = \App\Models\Jurusan::find($this->request->jurusan_id);
            if ($jurusan) {
                $query->whereHas('mahasiswa', function ($q) use ($jurusan) {
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

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Mahasiswa',
            'NIM',
            'Rata-rata',
            'Tanggal Penilaian'
        ];
    }

    public function map($penilaian): array
    {
        return [
            $penilaian->id,
            $penilaian->mahasiswa_nama,
            $penilaian->mahasiswa_nim,
            $penilaian->rata_rata,
            $penilaian->created_at->format('d/m/Y H:i'),
        ];
    }
}