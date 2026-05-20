<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    use HasFactory;

    protected $table = 'jurusan';

    protected $fillable = [
        'nama_jurusan',
        'slug',
        'deskripsi',
    ];

    public function prodi()
    {
        return $this->hasMany(Prodi::class, 'jurusan_id');
    }

    public function users()
    {
        return $this->hasManyThrough(User::class, Prodi::class, 'jurusan_id', 'prodi_id', 'id', 'id');
    }
}