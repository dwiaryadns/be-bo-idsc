<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailDistribusi extends Model
{
    use HasFactory;
    protected $primaryKey = 'detail_distribusi_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'detail_distribusi_id',
        'distribusi_id',
        'barang_id',
        'jumlah',
    ];

    public function distribusi()
    {
        return $this->belongsTo(Distribusi::class, 'distribusi_id', 'distribusi_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id', 'barang_id');
    }
}
