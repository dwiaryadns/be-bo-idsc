<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Icdx extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'sub_category',
        'en_name',
        'id_name'
    ];
}
