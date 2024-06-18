<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpEmail extends Model
{
    use HasFactory;

    protected $fillable = [
        'bisnis_owner_id',
        'otp',
        'expired_at'
    ];

    public function bisnis_owner()
    {
        return $this->belongsTo(BisnisOwner::class);
    }
}
