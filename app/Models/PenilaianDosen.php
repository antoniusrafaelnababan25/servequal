<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianDosen extends Model
{
    use HasFactory;

    protected $table = 'penilaian_dosen';

    protected $fillable = [
        'periode_id',
        'dosen_id',
        'dosen_nama',
        'mahasiswa_id',
        'mahasiswa_nama',
        'mahasiswa_nim',
        'kelas',
        'mata_kuliah',
        'nilai',
        'rata_rata',
    ];

    protected $casts = [
        'nilai' => 'array',
        'rata_rata' => 'decimal:2',
    ];

    public function periode()
    {
        return $this->belongsTo(KuesionerPeriode::class);
    }

    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }
}