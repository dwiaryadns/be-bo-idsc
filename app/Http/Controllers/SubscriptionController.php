<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    public function index($type)
    {
        $bo = Auth::guard('bisnis_owner')->user();
        if (!$bo) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        if (!in_array($type, ['need-pay', 'history'])) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid type parameter'
            ], 400);
        }

        $transactionStatuses = ($type == 'need-pay') ? ['pending'] : ['success', 'cancel', 'expired', 'failure', 'deny'];

        $transactions = Transaction::with('subscriptionPlan.fasyankes', 'payment')
            ->orderBy('transaction_time', 'DESC')
            ->whereIn('transaction_status', $transactionStatuses)
            ->whereHas('subscriptionPlan.fasyankes', function ($q) use ($bo) {
                $q->where('bisnis_owner_id', $bo->id);
            })
            ->get();

        $data = [];
        foreach ($transactions as $transaction) {
            if (!$transaction->subscriptionPlan) {
                continue;
            }

            $payment_type = $transaction->payment->payment_type == 'bank_transfer' ? strtoupper($transaction->payment->bank)  : $transaction->payment->payment_type;
            $fasyankes = $transaction->subscriptionPlan->fasyankes->name;
            $plan = $transaction->subscriptionPlan->package_plan;
            $duration = $transaction->subscriptionPlan->duration === 'Monthly' ? '1 Bulan' : '1 Tahun';

            $data_change = [
                'expired_at' => date('d M Y H:i', strtotime($transaction->payment->expired_at)),
                'amount' => 'Rp ' .  number_format($transaction->gross_amount, 2, ',', '.'),
                'transaction_time' => date('d M Y H:i', strtotime($transaction->transaction_time)),
                'payment_type' => $payment_type,
                'status' =>  ucfirst($transaction->transaction_status),
                'fasyankes' => $fasyankes,
                'plan' => $plan . ' ' . $duration,
                'qr_code' => $transaction->payment->url_qr,
                'va_number' => $transaction->payment->va_number,
                'transaction_id' => $transaction->transaction_id,
            ];
            $data[] = $data_change;
        }

        return response()->json([
            'status' => true,
            'message' => 'Success get transactions',
            'data' => $data,
        ]);
    }
}
