<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fasyankes extends Model
{
    use HasFactory;

    protected $table = 'fasyankes';
    protected $primaryKey = 'fasyankesId';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'fasyankesId',
        'bisnis_owner_id',
        'type',
        'warehouse_id',
        'name',
        'address',
        'pic',
        'pic_number',
        'email',
        'is_active',
    ];
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function bisnis_owner()
    {
        return $this->belongsTo(BisnisOwner::class);
    }

    public function access_fasyankes()
    {
        return $this->hasMany(AccessFasyankes::class);
    }
    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'password',
    ];
}
