<?php
// app/Services/TwilioService.php

namespace App\Services;
use Twilio\Rest\Client;

class TwilioService
{
    protected $sid;
    protected $token;
    protected $service_sid;


    public function __construct()
    {
        $this->sid = getenv("TWILIO_SID");
        $this->token = getenv("TWILIO_AUTH_TOKEN");
        $this->service_sid = getenv("TWILIO_SERVICE_SID");
    }


    public function sendMessage($to, $channel='sms', $from = 'Samel')
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://verify.twilio.com/v2/Services/$this->service_sid/Verifications",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_NOSIGNAL => 1,
            CURLOPT_CONNECTTIMEOUT => 20,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERPWD => $this->sid.":".$this->token,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array("To"=>"+$to","Channel"=>"$channel", "CustomCode" => "$from"),
            //CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded')
        ));
        $responseBody = json_decode(curl_exec($curl), true);
        curl_close($curl);
        return $responseBody;
    }


    public function verify($to,$Code)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://verify.twilio.com/v2/Services/$this->service_sid/VerificationCheck",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_NOSIGNAL => 1,
            CURLOPT_CONNECTTIMEOUT => 20,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERPWD => $this->sid.":".$this->token,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array("To"=>"+$to","Code"=>"$Code"),
//            CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded')
        ));
        $responseBody = json_decode(curl_exec($curl), true);

        curl_close($curl);
        return $responseBody;
    }
}
