<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_type',
        'transaction_id',
        'acquirer',
        'fraud_status',
        'expired_at',
        'va_number',
        'bank',
        'url_qr',
    ];

    protected $casts = [
        'expired_at' => 'datetime'
    ];
}
