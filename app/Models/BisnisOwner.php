<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PDO;

class BisnisOwner extends Authenticatable implements MustVerifyEmail

{
    use HasFactory, HasApiTokens, Notifiable;
    protected $table = 'bisnis_owners';
    protected $guard = 'bisnis_owner';
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_send_email',
        'is_resend',
        'is_first_login',
        'img_profile',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_send_email' => 'boolean',
        'is_resend' => 'boolean',
        'is_first_login' => 'boolean',
    ];

    public function bo_info()
    {
        return $this->hasOne(BoInfo::class);
    }

    public function legal_doc_bo()
    {
        return $this->hasOne(LegalDocBo::class);
    }

    public function warehouses()
    {
        return $this->hasMany(Warehouse::class);
    }

    public function fasyankes()
    {
        return $this->hasMany(Fasyankes::class);
    }

    public function suppliers()
    {
        return $this->hasMany(Supplier::class, 'bisnis_owner_id', 'id');
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
