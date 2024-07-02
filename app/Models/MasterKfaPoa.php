<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterKfaPoa extends Model
{
    use HasFactory;
    protected $primaryKey = 'kfa_poa_code';
    public $incrementing = false;
    // protected $keyType = 'unsignedBigInteger';

    protected $fillable = [
        'kfa_poa_code',
        'kfa_pov_code',
        'kfa_poa_idsc',
        'poa_desc',
        'manufacture',
        'generic_flag',
        'made_in',
        'kfa_code_poak',
        'pack_type',
        'estimate_pack_price'
    ];

    public function masterKfaPov()
    {
        return $this->belongsTo(MasterKfaPov::class, 'kfa_pov_code', 'kfa_pov_code');
    }
}
