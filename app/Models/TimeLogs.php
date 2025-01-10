<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeLogs extends Model
{

    protected $fillable = [
        'user_id',
        'project_id',
        'start_time',
        'end_time',
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function project()
    {
        return $this->belongsTo(Projects::class, 'project_id', 'id');
    }
}
