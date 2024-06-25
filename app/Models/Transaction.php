<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        // 'subscription_plan_id',
        'gross_amount',
        'transaction_time',
        'order_id',
        'transaction_status'
    ];

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    public function logTransactions()
    {
        return $this->hasMany(LogTransaction::class);
    }
}
