<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegalDocBo extends Model
{
    use HasFactory;

    protected $table = 'legal_doc_bo';
    protected $primaryKey = 'id';
    protected $fillable = [
        'bisnis_owner_id',
        'ktp',
        'akta',
        'sk_kemenkumham',
        'npwp',
        'nib',
        'iso',
        'status',
    ];

    public function bisnis_owner()
    {
        return $this->belongsTo(BisnisOwner::class);
    }
}
