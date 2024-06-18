<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'bisnis_owner_id',
        'businessId',
        'businessType',
        'businessName',
        'businessEmail',
        'phone',
        'mobile',
        'address',
        'province',
        'city',
        'subdistrict',
        'village',
        'postal_code',
        'status',
    ];

    public function bisnis_owner()
    {
        return $this->belongsTo(BisnisOwner::class);
    }
}
