<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriBarangApotek extends Model
{
    use HasFactory;

    protected $primaryKey = 'kategori_id';
    protected $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kategori_id',
        'nama'
    ];

    public function barangs()
    {
        return $this->hasMany(Barang::class, 'kategori_id', 'kategori_id');
    }
}
