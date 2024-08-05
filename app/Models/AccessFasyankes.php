<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class AccessFasyankes extends Model
{
    use HasFactory;

    protected $fillable = [
        'fasyankes_id',
        'id_profile',
        'username',
        'password',
        'is_active',
        'created_by',
        'role'
    ];

    public function fasyankes()
    {
        return $this->belongsTo(Fasyankes::class, 'fasyankes_id', 'fasyankesId');
    }
    public function wfid($fasyankes_id)
    {
        $fasyankesWarehouse = FasyankesWarehouse::where('fasyankes_id', $fasyankes_id)->first();
        return $fasyankesWarehouse->wfid;
    }
}
