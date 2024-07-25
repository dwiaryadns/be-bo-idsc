<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockBarang extends Model
{
    use HasFactory;

    protected $primaryKey = 'stok_barang_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'stok_barang_id', 'fasyankes_warehouse_id', 'barang_id', 'stok', 'stok_min'
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id', 'barang_id');
    }

    public function fasyankes_warehouse()
    {
        return $this->belongsTo(FasyankesWarehouse::class, 'fasyankes_warehouse_id', 'wfid');
    }
}
