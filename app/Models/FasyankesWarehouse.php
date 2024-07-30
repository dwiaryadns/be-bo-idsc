<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FasyankesWarehouse extends Model
{
    use HasFactory;

    protected $table = 'fasyankes_warehouse';
    protected $primaryKey = 'wfid';
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'wfid',
        'fasyankes_id',
        'warehouse_id',
    ];

    public function fasyankes()
    {
        return $this->belongsTo(Fasyankes::class, 'fasyankes_id', 'fasyankesId');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function stock_barangs()
    {
        return $this->hasMany(StockBarang::class, 'fasyankes_warehouse_id', 'wfid');
    }

    public function penerimaan_barangs()
    {
        return $this->hasMany(PenerimaanBarang::class, 'fasyankes_warehouse_id', 'wfid');
    }

    public function pembelian_barangs()
    {
        return $this->hasMany(Pembelian::class, 'fasyankes_warehouse_id', 'wfid');
    }
}
