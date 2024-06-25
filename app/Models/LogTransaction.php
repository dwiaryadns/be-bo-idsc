<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'status_message',
        'transaction_id',
        'transaction_status',
        'signature_key',
        'status_code',
        'response',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
