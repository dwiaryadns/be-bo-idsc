<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    protected $primaryKey = 'penjualan_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'penjualan_id',
        'total',
        'fasyankes_warehouse_id',
    ];

    public function fasyankesWarehouse()
    {
        return $this->belongsTo(FasyankesWarehouse::class, 'fasyankes_warehouse_id', 'wfid');
    }

    public function detailPenjualans()
    {
        return $this->hasMany(DetailPenjualan::class, 'penjualan_id', 'penjualan_id');
    }
}
