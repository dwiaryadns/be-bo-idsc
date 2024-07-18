<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenerimaanBarang extends Model
{
    use HasFactory;

    protected $primaryKey = 'penerimaan_id';
    public $incrementing = false;
    protected $keyType = 'string';


    protected $fillable = [
        'penerimaan_id', 'po_id', 'fasyankes_warehouse_id', 'tanggal_penerimaan', 'status', 'catatan', 'penerima', 'pengirim',
        'pengecek'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function good_receipt_note()
    {
        return $this->hasOne(GoodReceiptNote::class, 'penerimaan_id', 'penerimaan_id');
    }

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class, 'po_id', 'po_id');
    }

    public function fasyankes_warehouse()
    {
        return $this->belongsTo(FasyankesWarehouse::class, 'fasyankes_warehouse_id', 'wfid');
    }

    public function detail_penerimaan_barangs()
    {
        return $this->hasMany(DetailPenerimaanBarang::class, 'penerimaan_id', 'penerimaan_id');
    }

    public function detailPending()
    {
        return $this->hasMany(DetailPenerimaanBarang::class, 'penerimaan_id', 'penerimaan_id')
            ->with('barang')
            ->where('status', 'Retur');
    }
}
