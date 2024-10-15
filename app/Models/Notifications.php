<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    use HasFactory;
    protected $fillable = [
        'bisnis_owner_id',
        'title',
        'message',
        'type',
        'path',
        'is_read'
    ];

    public function bisnis_owners()
    {
        return $this->belongsTo(BisnisOwner::class);
    }
}
