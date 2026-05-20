<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    use HasFactory;

    protected $table = 'notifikasi';

    protected $fillable = [
        'type',
        'title',
        'message',
        'target_role',
        'target_user_id',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }
}