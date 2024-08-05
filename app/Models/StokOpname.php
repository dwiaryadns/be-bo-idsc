<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokOpname extends Model
{
    use HasFactory;

    protected $table = 'stok_opnames';

    protected $primaryKey = 'stok_opname_id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'stok_opname_id',
        'barang_id',
        'deskripsi',
        'tanggal_opname',
        'petugas',
        'stock_gudang_id',
        'stok_barang_id',
        'jml_tercatat',
        'jml_fisik',
        'jml_penyesuaian'
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id', 'barang_id');
    }

    public function stok_gudang()
    {
        return $this->belongsTo(StockGudang::class, 'stock_gudang_id', 'stock_gudang_id')->whereNotNull('stock_gudang_id');
    }

    public function stok_barang()
    {
        return $this->belongsTo(StockBarang::class, 'stok_barang_id', 'stok_barang_id')->whereNotNull('stok_barang_id');
    }

}
