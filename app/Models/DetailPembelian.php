<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPembelian extends Model
{
    use HasFactory;

    protected $primaryKey = 'detil_po_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'detil_po_id', 'notes', 'po_id', 'barang_id', 'jumlah', 'harga_satuan', 'total_harga'
    ];

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class, 'po_id', 'po_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id', 'barang_id');
    }
}
