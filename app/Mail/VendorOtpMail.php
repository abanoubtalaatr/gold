<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VendorOtpMail extends Mailable
{
public $otp;

public function __construct($otp)
{
$this->otp = $otp;
}

public function build()
{
return $this->markdown('emails.vendor-otp')
->subject('Your Vendor Registration OTP');
}
}