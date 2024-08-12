<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class DelegateAccess extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $table = 'delegate_accesses'; // Specify the table name
    protected $guard = 'delegate_access';
    protected $fillable = [
        'bisnis_owner_id',
        'role',
        'name',
        'is_verif',
        'is_active',
        'email',
        'password'
    ];
    protected $hidden = [
        'password',
    ];

    public function bisnis_owner()
    {
        return $this->belongsTo(BisnisOwner::class);
    }

    public function hak_akses()
    {
        return $this->hasOne(HakAkses::class);
    }
}
