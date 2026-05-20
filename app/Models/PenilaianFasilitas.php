<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianFasilitas extends Model
{
    use HasFactory;

    protected $table = 'penilaian_fasilitas';

    protected $fillable = [
        'periode_id',
        'mahasiswa_id',
        'mahasiswa_nama',
        'mahasiswa_nim',
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

    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }
}