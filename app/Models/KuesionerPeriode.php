<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
        'target_role',
        'target_prodi_id',
        'target_jenjang',
        'tujuan',
        'is_active',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ========== RELATIONS ==========

    /**
     * Relasi ke Prodi (untuk target_prodi_id)
     */
    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'target_prodi_id');
    }

    // ========== ACCESSORS ==========

    /**
     * Mendapatkan label status dalam bahasa Indonesia
     */
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'draft' => 'Draft',
            'aktif' => 'Aktif',
            'tutup' => 'Tutup',
            default => ucfirst($this->status),
        };
    }

    /**
     * Mendapatkan badge class untuk status
     */
    public function getStatusBadgeClassAttribute()
    {
        return match ($this->status) {
            'draft' => 'bg-secondary',
            'aktif' => 'bg-success',
            'tutup' => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    /**
     * Mendapatkan label target role
     */
    public function getTargetRoleLabelAttribute()
    {
        return match ($this->target_role) {
            'mahasiswa' => 'Mahasiswa',
            'dosen' => 'Dosen',
            'both' => 'Mahasiswa & Dosen',
            default => ucfirst($this->target_role),
        };
    }

    /**
     * Mendapatkan label target jenjang
     */
    public function getTargetJenjangLabelAttribute()
    {
        $jenjang = [
            'sarjana' => 'Sarjana (S1)',
            'pascasarjana' => 'Pascasarjana (S2)',
            'internasional' => 'Internasional',
            'all' => 'Semua Jenjang'
        ];
        return $jenjang[$this->target_jenjang] ?? ucfirst($this->target_jenjang);
    }

    /**
     * Mendapatkan target display (untuk ditampilkan di tabel)
     */
    public function getTargetDisplayAttribute()
    {
        $targets = [];

        if ($this->target_jurusan) {
            $targets[] = "Jurusan: {$this->target_jurusan}";
        }

        if ($this->target_prodi_id && $this->prodi) {
            $targets[] = "Prodi: {$this->prodi->nama_prodi}";
        }

        if ($this->target_jenjang && $this->target_jenjang != 'all') {
            $targets[] = "Jenjang: " . ucfirst($this->target_jenjang);
        }

        return $targets ? implode(', ', $targets) : 'Semua';
    }

    /**
     * Cek apakah periode sedang aktif (berdasarkan tanggal)
     */
    public function isCurrentlyActive()
    {
        $now = Carbon::now();
        return $this->status === 'aktif' &&
            $now->between($this->tanggal_mulai, $this->tanggal_selesai);
    }

    /**
     * Cek apakah periode bisa diisi
     */
    public function isAvailable()
    {
        return $this->is_active && $this->status === 'aktif' && $this->isCurrentlyActive();
    }

    // ========== SCOPES ==========

    /**
     * Scope untuk periode aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk periode berdasarkan status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk periode yang sedang berlangsung
     */
    public function scopeCurrent($query)
    {
        $now = Carbon::now();
        return $query->where('tanggal_mulai', '<=', $now)
            ->where('tanggal_selesai', '>=', $now)
            ->where('status', 'aktif');
    }
}