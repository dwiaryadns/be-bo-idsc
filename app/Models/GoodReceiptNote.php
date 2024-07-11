<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodReceiptNote extends Model
{
    use HasFactory;
    protected $primaryKey = 'grn_id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'penerimaan_id',
        'grn_id',
        'url_file',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function penerimaan()
    {
        return $this->belongsTo(PenerimaanBarang::class, 'penerimaan_id', 'penerimaan_id');
    }
}
