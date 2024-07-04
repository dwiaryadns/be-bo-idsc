<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPenerimaanBarang extends Model
{
    use HasFactory;
    protected $primaryKey = 'detil_penerimaan_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'detil_penerimaan_id', 'penerimaan_id', 'barang_id', 'jumlah', 'kondisi', 'catatan'
    ];

    public function penerimaan_barang()
    {
        return $this->belongsTo(PenerimaanBarang::class, 'penerimaan_id', 'penerimaan_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id', 'barang_id');
    }
}
