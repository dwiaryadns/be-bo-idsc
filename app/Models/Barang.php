<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $primaryKey = 'barang_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'barang_id', 'nama_barang', 'kategori_id', 'satuan', 'harga_beli', 'harga_jual', 'deskripsi'
    ];

    public function kategori_barang()
    {
        return $this->belongsTo(KategoriBarangApotek::class, 'kategori_id', 'kategori_id');
    }

    public function supplier_barangs()
    {
        return $this->hasMany(SupplierBarang::class, 'barang_id', 'barang_id');
    }

    public function detail_pembelians()
    {
        return $this->hasMany(DetailPembelian::class, 'barang_id', 'barang_id');
    }

    public function stock_barangs()
    {
        return $this->hasMany(StockBarang::class, 'barang_id', 'barang_id');
    }

    public function detail_penerimaan_barangs()
    {
        return $this->hasMany(DetailPenerimaanBarang::class, 'barang_id', 'barang_id');
    }
}
