<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    use HasFactory;

    protected $primaryKey = 'po_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'po_id', 'supplier_id', 'fasyankes_warehouse_id', 'tanggal_po', 'status', 'total_harga', 'catatan'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }

    public function fasyankes_warehouse()
    {
        return $this->belongsTo(FasyankesWarehouse::class, 'fasyankes_warehouse_id', 'wfid');
    }

    public function detail_pembelians()
    {
        return $this->hasMany(DetailPembelian::class, 'po_id', 'po_id');
    }

    public function penerimaan_barangs()
    {
        return $this->hasMany(PenerimaanBarang::class, 'po_id', 'po_id');
    }
}
