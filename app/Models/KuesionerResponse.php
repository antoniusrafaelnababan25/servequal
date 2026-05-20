<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KuesionerResponse extends Model
{
    use HasFactory;

    protected $table = 'kuesioner_responses';

    protected $fillable = [
        'periode_id',
        'responden_id',
        'responden_nama',
        'responden_nim',
        'responden_nidn',
        'role',
        'kelas',
        'mata_kuliah',
        'dosen_id',
        'dosen_nama',
        'jawaban',
        'rata_rata',
    ];

    protected $casts = [
        'jawaban' => 'array',
        'rata_rata' => 'decimal:2',
    ];

    public function periode()
    {
        return $this->belongsTo(KuesionerPeriode::class);
    }

    public function responden()
    {
        return $this->belongsTo(User::class, 'responden_id');
    }

    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }
}