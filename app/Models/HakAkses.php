<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HakAkses extends Model
{
    use HasFactory;

    protected $fillable = [
        'delegate_access_id',
        'persmission',
    ];

    public function delegate_access()
    {
        return $this->belongsTo(DelegateAccess::class);
    }
}
