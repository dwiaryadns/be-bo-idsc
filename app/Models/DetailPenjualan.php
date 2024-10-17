<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPenjualan extends Model
{
    use HasFactory;

    protected $primaryKey = 'detail_penjualan_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'detail_penjualan_id',
        'barang_id',
        'jumlah',
        'harga_satuan',
        'total_harga',
        'penjualan_id',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id', 'barang_id');
    }

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'penjualan_id', 'penjualan_id');
    }
}
