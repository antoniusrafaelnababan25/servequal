<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pertanyaan extends Model
{
    use HasFactory;

    protected $table = 'pertanyaan';

    // Dimensi SERVQUAL
    const DIMENSI_TANGIBLE = 'Tangible';
    const DIMENSI_RELIABILITY = 'Reliability';
    const DIMENSI_RESPONSIVENESS = 'Responsiveness';
    const DIMENSI_ASSURANCE = 'Assurance';
    const DIMENSI_EMPATHY = 'Empathy';

    const DIMENSIONS = [
        self::DIMENSI_TANGIBLE => 'Tangible (Fisik)',
        self::DIMENSI_RELIABILITY => 'Reliability (Keandalan)',
        self::DIMENSI_RESPONSIVENESS => 'Responsiveness (Daya Tanggap)',
        self::DIMENSI_ASSURANCE => 'Assurance (Jaminan)',
        self::DIMENSI_EMPATHY => 'Empathy (Empati)',
    ];

    // Tipe Penilaian
    const TYPE_PENILAIAN_DOSEN = 'penilaian_dosen';
    const TYPE_PENILAIAN_FASILITAS = 'penilaian_fasilitas';

    const TYPES = [
        self::TYPE_PENILAIAN_DOSEN => 'Penilaian Dosen',
        self::TYPE_PENILAIAN_FASILITAS => 'Penilaian Fasilitas',
    ];

    // Target Role
    const TARGET_MAHASISWA = 'mahasiswa';
    const TARGET_DOSEN = 'dosen';
    const TARGET_BOTH = 'both';

    const TARGET_ROLES = [
        self::TARGET_MAHASISWA => '🎓 Mahasiswa',
        self::TARGET_DOSEN => '👨‍🏫 Dosen',
        self::TARGET_BOTH => '👥 Mahasiswa & Dosen',
    ];

    // Kategori Fasilitas
    const KATEGORI_UMUM = 'umum';
    const KATEGORI_PERALATAN = 'peralatan';
    const KATEGORI_RUANGAN = 'ruangan';
    const KATEGORI_AKSES = 'akses';
    const KATEGORI_INFRASTRUKTUR = 'infrastruktur';

    const KATEGORI_FASILITAS = [
        self::KATEGORI_UMUM => '🏢 Umum',
        self::KATEGORI_PERALATAN => '🖥️ Peralatan & Perlengkapan',
        self::KATEGORI_RUANGAN => '🏠 Ruangan & Kelas',
        self::KATEGORI_AKSES => '🚪 Akses & Layanan',
        self::KATEGORI_INFRASTRUKTUR => '🏗️ Infrastruktur',
    ];

    protected $fillable = [
        'dimensi',
        'teks',
        'target_role',
        'tipe_penilaian',
        'kategori_fasilitas',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ========== ACCESSORS ==========

    public function getTipePenilaianLabelAttribute()
    {
        return self::TYPES[$this->tipe_penilaian] ?? 'Tidak Diketahui';
    }

    public function getTargetRoleLabelAttribute()
    {
        return self::TARGET_ROLES[$this->target_role] ?? ucfirst($this->target_role);
    }

    public function getTargetRoleBadgeAttribute()
    {
        $badges = [
            self::TARGET_MAHASISWA => '<span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2"><i class="bi bi-person me-1"></i> Mahasiswa</span>',
            self::TARGET_DOSEN => '<span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-2"><i class="bi bi-person-badge me-1"></i> Dosen</span>',
            self::TARGET_BOTH => '<span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2"><i class="bi bi-people me-1"></i> Mahasiswa & Dosen</span>',
        ];
        return $badges[$this->target_role] ?? '<span class="badge bg-secondary">Unknown</span>';
    }

    public function getKategoriFasilitasLabelAttribute()
    {
        return self::KATEGORI_FASILITAS[$this->kategori_fasilitas] ?? 'Umum';
    }

    public function getDimensiLabelAttribute()
    {
        return self::DIMENSIONS[$this->dimensi] ?? $this->dimensi;
    }

    public function getShortTeksAttribute()
    {
        return strlen($this->teks) > 100 ? substr($this->teks, 0, 100) . '...' : $this->teks;
    }

    // ========== SCOPES ==========

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForMahasiswa($query)
    {
        return $query->whereIn('target_role', [self::TARGET_MAHASISWA, self::TARGET_BOTH]);
    }

    public function scopeForDosen($query)
    {
        return $query->whereIn('target_role', [self::TARGET_DOSEN, self::TARGET_BOTH]);
    }

    // ========== HELPERS ==========

    public static function getForMahasiswaPenilaianDosen()
    {
        return self::active()
            ->where('tipe_penilaian', self::TYPE_PENILAIAN_DOSEN)
            ->whereIn('target_role', [self::TARGET_MAHASISWA, self::TARGET_BOTH])
            ->orderBy('dimensi')
            ->orderBy('id')
            ->get();
    }

    public static function getForDosenPenilaianFasilitas()
    {
        return self::active()
            ->where('tipe_penilaian', self::TYPE_PENILAIAN_FASILITAS)
            ->whereIn('target_role', [self::TARGET_DOSEN, self::TARGET_BOTH])
            ->orderBy('kategori_fasilitas')
            ->orderBy('dimensi')
            ->orderBy('id')
            ->get();
    }
}