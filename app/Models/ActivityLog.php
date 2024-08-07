<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'activity',
        'menu',
        'activity_by',
        'activity_at',
        'status'
    ];
}
