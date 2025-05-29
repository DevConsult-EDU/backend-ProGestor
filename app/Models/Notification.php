<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasUuids;
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'content',
        'link',
        'read',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'read' => 'boolean', // ¡ESTA LÍNEA ES LA CLAVE!
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
