<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessConsole extends Model
{
    use HasFactory;

    protected $fillable = [
        'fullname',
        'email',
        'role'
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    
}
