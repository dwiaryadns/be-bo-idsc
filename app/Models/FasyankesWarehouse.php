<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FasyankesWarehouse extends Model
{
    use HasFactory;

    protected $table = 'fasyankes_warehouse';
    protected $primaryKey = 'wfid';
    protected $fillable = [
        'wfid',
        'fasyankes_id',
        'warehouse_id',
    ];

    public function fasyankes()
    {
        return $this->belongsTo(Fasyankes::class, 'fasyankes_id', 'fasyankesId');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
