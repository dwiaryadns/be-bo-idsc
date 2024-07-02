<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterKfaPov extends Model
{
    use HasFactory;

    protected $primaryKey = 'kfa_pov_code';
    public $incrementing = false;
    // protected $keyType = 'unsignedBigInteger';

    protected $fillable = [
        'kfa_pov_code',
        'kfa_code',
        'kfa_pov_idsc',
        'product_state',
        'pov_desc'
    ];

    public function masterKfa()
    {
        return $this->belongsTo(MasterKfa::class, 'kfa_code', 'kfa_code');
    }

    public function kfaPoas()
    {
        return $this->hasMany(MasterKfaPoa::class, 'kfa_pov_code', 'kfa_pov_code');
    }
}
