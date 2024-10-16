<?php

namespace App\Http\Controllers;

use App\Models\AccessFasyankes;
use App\Models\LogTransaction;
use App\Models\Payment;
use App\Models\SubscriptionPlan;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Notification;
use Midtrans\Snap;

class PaymentController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function createTransaction(Request $request)
    {
        Log::info($request->amount . ' ' . (int)$request->amount . ' ' . (float)$request->amount);
        $y = date('Y');
        $m = date('m');
        $params = [
            'transaction_details' => [
                'order_id' => uniqid(),
                'gross_amount' => str_replace('.', '', $request->amount),
            ],
            "item_details" => [
                [
                    "id" => "IDSC" . $y . $m . rand(100000, 999999),
                    // "price" => str_replace('.', '', $request->amount),
                    "price" => 5000,
                    "quantity" => 1,
                    "name" => $request->type,
                    "brand" => "IdSmartCare",
                    "category" => $request->package,
                    "merchant_name" => "IdsmartCare",
                    "url" => ""
                ]
            ],
            "customer_details" => [
                "first_name" => $request->name,
                "last_name" => "",
                "email" => $request->email,
                "phone" => $request->pic_number,
            ],
            'enabled_payments' => ['bank_transfer', 'gopay'],
            'expiry' => [
                'start_time' => date('Y-m-d H:i:s T', strtotime('now')),
                'unit' => 'minutes',
                'duration' => 1440
            ],
            'custom_field1' => $request->subscription_plan_id,
        ];

        Log::info('Transaction Params: ', $params);

        try {
            $snapToken = Snap::getSnapToken($params);
            Log::info('Snap Token: ', ['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            Log::error('Error creating transaction: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Error creating transaction']);
        }

        return response()->json(['status' => true, 'message' => 'Success Get Token', 'snap_token' => $snapToken]);
    }

    public function handlePayment($transaction, $notification)
    {
        if ($transaction) {
            $paymentType = $notification->payment_type ?? null;
            $acquirer = $notification->acquirer ?? null;
            $fraudStatus = $notification->fraud_status ?? null;
            $expiryTime = $notification->expiry_time ?? null;

            // Ambil URL QR code dari notifikasi jika ada
            $urlQr = isset($notification->actions) ? collect($notification->actions)->firstWhere('name', 'generate-qr-code')['url'] : null;

            $vaNumber = isset($notification->va_numbers[0]) ? $notification->va_numbers[0]->va_number : null;
            $bank = isset($notification->va_numbers[0]) ? $notification->va_numbers[0]->bank : null;

            $payment = Payment::updateOrCreate([
                'transaction_id' => $transaction->id,
            ], [
                'payment_type' => $paymentType,
                'acquirer' => $acquirer,
                'fraud_status' => $fraudStatus,
                'expired_at' => $expiryTime,
                'va_number' => $vaNumber,
                'bank' => $bank,
                'url_qr' => $urlQr // Simpan QR URL di database
            ]);

            Log::info('Payment : ' . $payment);
        }
    }


    public function handleLogTransaction($transaction, $notification)
    {
        if ($transaction) {
            $logTransaction = LogTransaction::create([
                'status_message' => $notification->status_message ?? null,
                'signature_key' => $notification->signature_key ?? null,
                'transaction_id' => $transaction->id ?? null,
                'status_code' => $notification->status_code ?? null,
                'transaction_status' => $notification->transaction_status,
            ]);
            Log::info('Log Transaction : ' . $logTransaction);
        }
    }

    public function handleNotification(Request $request)
    {
        try {
            Log::info('Midtrans notification received', $request->all());

            $notification = new Notification();

            $transactionStatus = $notification->transaction_status;
            $orderId = $notification->order_id;
            $fraudStatus = $notification->fraud_status;
            $transactionTime = $notification->transaction_time;
            $transactionId = $notification->transaction_id;
            $grossAmount = $notification->gross_amount;
            $customField1 = $notification->custom_field1;

            $transaction = Transaction::where('order_id', $orderId)->first();

            if (!$transaction) {
                $transaction = new Transaction();
                $transaction->order_id = $orderId;
            }

            $transaction->transaction_status = $transactionStatus;
            $transaction->transaction_time = $transactionTime;
            $transaction->transaction_id = $transactionId;
            $transaction->gross_amount = $grossAmount;
            $transaction->subscription_plan_id = $customField1;


            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'challenge') {
                    $transaction->transaction_status = 'challenge';
                } else {
                    $transaction->transaction_status = 'success';
                }
            } elseif ($transactionStatus == 'settlement') {
                $transaction->transaction_status = 'success';
            } elseif ($transactionStatus == 'pending') {

                $transaction->transaction_status = 'pending';
            } elseif ($transactionStatus == 'deny') {
                $transaction->transaction_status = 'failed';
            } elseif ($transactionStatus == 'expire') {
                $transaction->transaction_status = 'expired';
            } elseif ($transactionStatus == 'cancel') {
                $transaction->transaction_status = 'canceled';
            }
            $transaction->save();

            $this->handlePayment($transaction, $notification);
            $this->handleLogTransaction($transaction, $notification);
            Log::info(json_encode($notification, true));

            if ($transaction->transaction_status == 'success') {
                $getSubscription = SubscriptionPlan::with('fasyankes')
                    ->where('id', $transaction->subscription_plan_id)
                    ->first();
                if ($getSubscription) {
                    $fasyankes = $getSubscription->fasyankes;
                    $fasyankes->update(['is_active' => 1]);
                    if ($fasyankes) {
                        $accessFasyankes = AccessFasyankes::where('role', 'admin')
                            ->where('fasyankes_id', $fasyankes->fasyankesId)
                            ->first();
                        $accessFasyankes->update(['is_active' => 1]);
                    }
                }
            }

            return response()->json(['message' => 'Notification processed successfully']);
        } catch (\Throwable $th) {
            Log::error('Error handling notification: ' . $th->getMessage());
            return response()->json(['message' => 'Error handling notification'], 500);
        }
    }
}

 // public function handlePayment($transaction, $notification)
    // {
    //     if ($transaction) {
    //         $paymentType = $notification->payment_type ?? null;
    //         $acquirer = $notification->acquirer ?? null;
    //         $fraudStatus = $notification->fraud_status ?? null;
    //         $expiryTime = $notification->expiry_time ?? null;
    //         // $qris = $notication->action ?? null;
    //         // Log::info($qris);
    //         $urlQr = null;
    //         if ($acquirer == 'airpay shopee') {
    //             $urlQr = "https://api.sandbox.midtrans.com/v2/qris/shopeepay/sppq_" . $transaction->transaction_id . "/qr-code";
    //         } else if ($acquirer == 'gopay') {
    //             $urlQr = "https://api.sandbox.midtrans.com/v2/qris/" . $transaction->transaction_id . "/qr-code";
    //         }
    //         // if ($qris != null) {
    //         //     $getQrF = $qris['generate-qr-code'];
    //         //     if ($getQrF) {
    //         //         $urlQr = $qris['url'];
    //         //     }
    //         // }
    //         $vaNumber = isset($notification->va_numbers[0]) ? $notification->va_numbers[0]->va_number : null;
    //         $bank = isset($notification->va_numbers[0]) ? $notification->va_numbers[0]->bank : null;

    //         $payment = Payment::updateOrCreate([
    //             'transaction_id' => $transaction->id,
    //         ], [
    //             'payment_type' => $paymentType,
    //             'acquirer' => $acquirer,
    //             'fraud_status' => $fraudStatus,
    //             'expired_at' => $expiryTime,
    //             'va_number' => $vaNumber,
    //             'bank' => $bank,
    //             'url_qr' => $urlQr
    //         ]);

    //         Log::info('Payment : ' . $payment);
    //     }
    // }