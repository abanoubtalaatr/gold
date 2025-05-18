<?php
namespace App\Services\SMS;

use Illuminate\Support\Str;

class Msegat
{
    private $username;
    private $key;
    private $sender_name;
    private $mesgat_base_url;

    public function __construct()
    {
        $this->mesgat_base_url = config('services.msegat.base_url');
        $this->username = config('services.msegat.username');
        $this->key = config('services.msegat.key');
        $this->sender_name = config('services.msegat.sender_name');
    }

    public function send_sms($mobile, $msg)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->mesgat_base_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        
        $mobile = mobileHandler($mobile); // Ensure mobileHandler is defined

        $fields = json_encode([
            'userName' => $this->username,
            'userSender' => $this->sender_name,
            'numbers' => $mobile,
            'apiKey' => $this->key,
            'msg' => $msg,
        ]);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        
        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        
        $value = Str::between($response, 'message":"', '"}');
        $value_code = Str::between($response, 'code":"', '","message');

        return ['response' => $value, 'response_code' => $value_code];
    }
}