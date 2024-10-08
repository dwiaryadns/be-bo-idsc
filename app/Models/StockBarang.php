<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockBarang extends Model
{
    use HasFactory;

    protected $primaryKey = 'stok_barang_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'stok_barang_id',
        'fasyankes_warehouse_id',
        'barang_id',
        'stok',
        'stok_min',
        'harga_jual'
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id', 'barang_id');
    }

    public function fasyankes_warehouse()
    {
        return $this->belongsTo(FasyankesWarehouse::class, 'fasyankes_warehouse_id', 'wfid');
    }
    public function stok_opname()
    {
        return $this->hasMany(StokOpname::class, 'stok_barang_id', 'stok_barang_id');
    }
    public function latestStokOpname()
    {
        return $this->hasOne(StokOpname::class, 'stok_barang_id', 'stok_barang_id')
            ->latest('created_at');
    }

    public function diskon()
    {
        return $this->hasOne(Diskon::class, 'stok_barang_id', 'stok_barang_id')
            ->where('expired_disc', '>=', Carbon::now());
    }
}
