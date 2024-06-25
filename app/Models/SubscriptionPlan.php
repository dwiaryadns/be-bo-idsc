<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'fasyankes_id',
        'package_plan',
        'duration',
        'price',
        'start_date',
        'end_date',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
