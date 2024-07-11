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
        'barang_id', 'kfa_poa_code', 'nama_barang', 'kategori_id', 'satuan', 'harga_beli', 'harga_jual', 'deskripsi', 'supplier_id'
    ];
    protected $hidden = ['created_at', 'updated_at'];


    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }
    public function kategori_barang()
    {
        return $this->belongsTo(KategoriBarangApotek::class, 'kategori_id', 'kategori_id')->select('kategori_id', 'nama');
    }

    public function kfa_poa()
    {
        return $this->belongsTo(MasterKfaPoa::class, 'kfa_poa_code', 'kfa_poa_code')->whereNotNull('kfa_poa_code')
            ->whereNotNull('kfa_poa_code');
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
