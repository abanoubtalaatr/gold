<?php 

namespace App\Http\Services;

class MesgatService
{
    function send_sms($numbers, $msg)
    {
        $data = [
            "userName" => env('MESGAT_USER_NAME'),
            "password" => env('MESGAT_PASSWORD'),
            "userSender" => env('MESGAT_SENDER_NAME'),
            "numbers" => $numbers,
            "apiKey" => env('MESGAT_KEY'),
            "msg" => $msg,
            "msgEncoding" => "UTF8",
        ];
        $client = new \GuzzleHttp\Client();
        $res = $client->request('POST', 'https://www.msegat.com/gw/sendsms.php', [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Accept-Language' => app()->getLocale() == 'ar' ? 'ar-Sa' : 'en-Uk',
            ],
            'body' => json_encode($data),
        ]);

        if ($res) {
            $data = json_decode($res->getBody()->getContents());
            info(print_r($data, true));
            if (isset($data->code)) {
                //----- if code == 1 => Success, otherwise failed.
                $code = $data->code;
                $message = $data->message;
                return $code == 1 ? 1 : $code;
                // return response()->json(['code'=> $code,'message' => __($message)]);
            }
            return 0;
        }
        return 0;
    }
}