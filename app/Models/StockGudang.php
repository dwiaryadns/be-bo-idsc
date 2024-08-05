<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockGudang extends Model
{
    use HasFactory;

    protected $primaryKey = 'stock_gudang_id';
    public $incrementing = false;
    protected $keyType = 'string';


    protected $fillable = [
        'stock_gudang_id',
        'warehouse_id',
        'barang_id',
        'stok',
        'isJual',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id', 'barang_id');
    }

    public function stok_opname()
    {
        return $this->hasMany(StokOpname::class, 'stock_gudang_id', 'stock_gudang_id');
    }
    public function latestStokOpname()
    {
        return $this->hasOne(StokOpname::class, 'stock_gudang_id', 'stock_gudang_id')
            ->latest('created_at');
    }
    
}
