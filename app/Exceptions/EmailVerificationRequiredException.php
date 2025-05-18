<?php

namespace App\Exceptions;

use Exception;

class EmailVerificationRequiredException extends Exception
{
    protected $statusCode;

    public function __construct($message = 'Email verification required', $statusCode = 409)
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
    }

    public function render($request)
    {
        return response()->json([
            'message' => $this->getMessage(),
            'otp_sent' => true,
        ], $this->statusCode);
    }
}