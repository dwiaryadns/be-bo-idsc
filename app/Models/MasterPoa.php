<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterPoa extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $primaryKey = 'id_idsc';
    protected $fillable = [
        'id_idsc',
        'poa_code',
        'pov',
        'poa'
    ];

    protected $keyType = 'string';
}
