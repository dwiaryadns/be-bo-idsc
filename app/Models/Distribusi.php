<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Distribusi extends Model
{
    use HasFactory;

    protected $primaryKey = 'distribusi_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'distribusi_id',
        'warehouse_id',
        'fasyankes_id',
        'date',
        'status',
        'keterangan',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function fasyankes()
    {
        return $this->belongsTo(Fasyankes::class, 'fasyankes_id', 'fasyankesId');
    }

    public function detail_distribusi()
    {
        return $this->hasMany(DetailDistribusi::class, 'distribusi_id', 'distribusi_id');
    }
}
