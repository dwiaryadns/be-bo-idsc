<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $primaryKey = 'supplier_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'supplier_id', 'nama_supplier', 'alamat', 'kota', 'provinsi', 'kode_pos', 'negara',
        'nomor_telepon', 'email', 'website', 'kontak_person', 'nomor_kontak_person', 'email_kontak_person',
        'tipe_supplier', 'nomor_npwp', 'tanggal_kerjasama', 'catatan_tambahan'
    ];

    public function supplier_barangs()
    {
        return $this->hasMany(SupplierBarang::class, 'supplier_id', 'supplier_id');
    }

    public function pembelians()
    {
        return $this->hasMany(Pembelian::class, 'supplier_id', 'supplier_id');
    }
}