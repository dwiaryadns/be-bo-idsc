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
        'username',
        'is_active',
        'password',
        // 'sector',
        // 'duration',
        // 'package_plan',
    ];
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'password',
    ];
}
