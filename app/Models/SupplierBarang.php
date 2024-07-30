<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierBarang extends Model
{
    use HasFactory;

    protected $primaryKey = 'supplier_barang_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'supplier_barang_id', 'supplier_id', 'barang_id', 'harga'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id', 'barang_id');
    }
}
