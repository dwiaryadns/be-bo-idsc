<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;
    protected $fillable = [
        'bisnis_owner_id',
        'name',
        'address',
        'pic',
        'contact',
    ];

    public function bisnis_owner()
    {
        return $this->belongsTo(BisnisOwner::class);
    }

    public function fasyankes()
    {
        return $this->hasMany(Fasyankes::class)->where('is_active', 1);
    }
}
