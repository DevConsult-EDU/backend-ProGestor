<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
      'id',
      'project_id',
      'title',
      'description',
      'status',
      'priority',
      'user_id',
      'due_date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'due_date' => 'datetime', // Or 'date' if it's just a date without time
        'another_date_field' => 'datetime:Y-m-d', // Cast with a specific format
        // 'created_at' and 'updated_at' are usually cast by default
        'created_at' => 'datetime',
    ];
}
