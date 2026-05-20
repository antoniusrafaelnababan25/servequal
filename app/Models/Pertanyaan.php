<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pertanyaan extends Model
{
    use HasFactory;

    // Tentukan nama tabel secara eksplisit
    protected $table = 'pertanyaan';

    protected $fillable = [
        'dimensi',
        'teks',
        'target_role',
        'is_active',
    ];


    protected $casts = [
        'is_active' => 'boolean',
    ];

    const DIMENSI_TANGIBLE = 'Tangible';
    const DIMENSI_RELIABILITY = 'Reliability';
    const DIMENSI_RESPONSIVENESS = 'Responsiveness';
    const DIMENSI_ASSURANCE = 'Assurance';
    const DIMENSI_EMPATHY = 'Empathy';

    const DIMENSIONS = [
        self::DIMENSI_TANGIBLE,
        self::DIMENSI_RELIABILITY,
        self::DIMENSI_RESPONSIVENESS,
        self::DIMENSI_ASSURANCE,
        self::DIMENSI_EMPATHY,
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForMahasiswa($query)
    {
        return $query->where('target_role', 'mahasiswa');
    }

    public function scopeForDosen($query)
    {
        return $query->where('target_role', 'dosen');
    }
}