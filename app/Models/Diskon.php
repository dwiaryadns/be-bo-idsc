<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diskon extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'stok_barang_id',
        'percent_disc',
        'amount_disc',
        'expired_disc'
    ];

    public function stok_barang()
    {
        return $this->belongsTo(StockBarang::class, 'stok_barang_id', 'stok_barang_id');
    }
}
