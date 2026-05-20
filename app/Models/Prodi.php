<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Prodi extends Model
{
    use HasFactory;

    protected $table = 'prodi';

    protected $fillable = [
        'jurusan_id',
        'nama_prodi',
        'jenjang',
        'slug',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Boot: auto-generate slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($prodi) {
            if (empty($prodi->slug)) {
                $prodi->slug = Str::slug($prodi->nama_prodi);
            }
        });

        static::updating(function ($prodi) {
            if ($prodi->isDirty('nama_prodi') && empty($prodi->slug)) {
                $prodi->slug = Str::slug($prodi->nama_prodi);
            }
        });
    }

    // ========== RELATIONS ==========
    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'prodi_id');
    }

    // ========== SCOPES ==========
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ========== ACCESSORS ==========
    public function getNamaLengkapAttribute()
    {
        $jenjangLabel = match ($this->jenjang) {
            'sarjana' => 'S1',
            'pascasarjana' => 'S2',
            'internasional' => 'Internasional',
            default => ucfirst($this->jenjang),
        };
        return $jenjangLabel . ' - ' . $this->nama_prodi;
    }
}