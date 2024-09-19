<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterPoa extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_idsc',
        'poa',
        'pov',
        'poa_code',
    ];
}
