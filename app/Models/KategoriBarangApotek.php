<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriBarangApotek extends Model
{
    use HasFactory;

    protected $fillable = [
        'kategori_id',
        'nama'
    ];
}
