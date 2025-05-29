<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $table = 'projects';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
      'name',
      'description',
      'customer_id',
      'status',
      'started_at',
      'finished_at',
      'created_at',
      'updated_at',
    ];

     public function tasks()
     {
        return $this->hasMany(Task::class);
     }
}
