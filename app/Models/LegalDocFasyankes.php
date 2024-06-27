<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegalDocFasyankes extends Model
{
    use HasFactory;

    protected $table = "legal_doc_fasyankes";

    protected $fillable = [
        'fasyankes_id',
        'sia',
        'sipa',
        'simk',
        'siok',
    ];

    public function fasyankes()
    {
        return $this->belongsTo(Fasyankes::class);
    }
}
