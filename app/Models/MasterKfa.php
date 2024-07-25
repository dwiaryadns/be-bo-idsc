<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterKfa extends Model
{
    use HasFactory;

    protected $primaryKey = 'kfa_code';
    public $incrementing = false;
    protected $keyType = 'unsignedBigInteger';

    protected $fillable = [
        'kfa_code',
        'bza_desc',
        'kfa_code_idsc'
    ];
    
    public function kfaPovs()
    {
        return $this->hasMany(MasterKfaPov::class, 'kfa_code', 'kfa_code');
    }
}
