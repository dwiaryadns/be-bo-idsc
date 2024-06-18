<?php

namespace App\Jobs;

use App\Mail\SendOtpMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOtpEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $email;
    public $otp;

    public function __construct($email, $otp)
    {
        $this->email = $email;
        $this->otp = $otp;
    }

    public function handle()
    {
        Log::info("Sending OTP email to {$this->email}");

        try {
            if ($this->otp === null) {
                Log::error('OTP is null');
            }
            if ($this->email === null) {
                Log::error('Email is null');
            }
            Mail::to($this->email)->send(new SendOtpMail($this->otp));
            Log::info("OTP email sent successfully to {$this->email}");
        } catch (\Exception $e) {
            Log::error("Failed to send OTP email to {$this->email}: {$e->getMessage()}");
        }
    }
}
