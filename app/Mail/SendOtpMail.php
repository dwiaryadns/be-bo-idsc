<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;

    public function __construct($otp)
    {
        $this->otp = $otp;

        Log::info('SendOtpMail constructed with OTP: ' . $otp);

        if ($otp === null) {
            Log::error('OTP is null in SendOtpMail constructor');
        }
    }

    public function build()
    {
        Log::info('Building email with OTP: ' . $this->otp);
        $otp = $this->otp;
        return $this->from(env('MAIL_FROM_ADDRESS', 'idsmartcare-test@ayobisaindonesia.com'), 'IdSmartCare')
            ->subject('IdSmartCare - OTP')
            ->view('otp-email', compact('otp'));
    }
}
