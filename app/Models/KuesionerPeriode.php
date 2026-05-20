<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KuesionerPeriode extends Model
{
    use HasFactory;

    protected $table = 'kuesioner_periode';

    protected $fillable = [
        'nama_periode',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'target_jurusan',
        'tujuan',
        'is_active',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'is_active' => 'boolean',
    ];
}