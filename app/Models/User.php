<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'username',
    'name',
    'email',
    'password',
    'role',
    'nidn',
    'nim',
    'kelas',
    'jurusan',
    'prodi_id',          // ← TAMBAHKAN INI
    'avatar',
    'tanggal_lahir',
    'last_login',
    'is_active'
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'tanggal_lahir' => 'date',
            'last_login' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->role)) {
                $user->role = 'mahasiswa';
            }
        });
    }

    // ========== RELATIONS ==========

    // Relasi ke Prodi
    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'prodi_id');
    }

    // Helper untuk mengakses nama jurusan melalui prodi
    public function getNamaJurusanAttribute()
    {
        return $this->prodi?->jurusan?->nama_jurusan;
    }

    // Relasi lainnya (tidak berubah)
    public function penilaianSebagaiDosen()
    {
        return $this->hasMany(PenilaianDosen::class, 'dosen_id');
    }

    public function penilaianSebagaiMahasiswa()
    {
        return $this->hasMany(PenilaianDosen::class, 'mahasiswa_id');
    }

    public function penilaianFasilitas()
    {
        return $this->hasMany(PenilaianFasilitas::class, 'mahasiswa_id');
    }

    public function kuesionerResponses()
    {
        return $this->hasMany(KuesionerResponse::class, 'responden_id');
    }

    public function logAktivitas()
    {
        return $this->hasMany(LogAktivitas::class, 'user_id');
    }

    public function notifikasiTarget()
    {
        return $this->hasMany(Notifikasi::class, 'target_user_id');
    }

    // ========== SCOPES ==========

    public function scopeRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDosen($query)
    {
        return $query->where('role', 'dosen');
    }

    public function scopeMahasiswa($query)
    {
        return $query->where('role', 'mahasiswa');
    }

    public function scopeAdmin($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeSuperAdmin($query)
    {
        return $query->where('role', 'super_admin');
    }

    public function scopeByJurusan($query, $jurusan)
    {
        return $query->where('jurusan', $jurusan);
    }

    // ========== HELPERS ==========

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isDosen(): bool
    {
        return $this->role === 'dosen';
    }

    public function isMahasiswa(): bool
    {
        return $this->role === 'mahasiswa';
    }

    public static function getDosenByJurusan($jurusan)
    {
        return self::where('role', 'dosen')
            ->where('jurusan', $jurusan)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public static function getMahasiswaByJurusan($jurusan)
    {
        return self::where('role', 'mahasiswa')
            ->where('jurusan', $jurusan)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->avatar && file_exists(public_path('storage/' . $this->avatar))) {
                    return asset('storage/' . $this->avatar);
                }
                return asset('images/default-avatar.png');
            }
        );
    }

    public function updateLastLogin()
    {
        $this->last_login = now();
        $this->save();
    }
}