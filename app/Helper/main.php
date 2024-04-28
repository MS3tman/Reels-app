<?php

use Pusher\Pusher;

function generateRandomCoupon($length = 7) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $coupon = '';

    // Generate random characters
    for ($i = 0; $i < $length; $i++) {
        $coupon .= $characters[rand(0, strlen($characters) - 1)];
    }

    // Append time-based component (current timestamp)
    $currentTime = time();  // Current UNIX timestamp
    $timeComponent = substr(strval($currentTime), -5);  // Last 5 digits of the timestamp
    $coupon .= $timeComponent;

    return $coupon;
    // Example usage:
    // $couponCode = generateRandomCoupon();
    // echo $couponCode;
}

function view_price(){
    return 0.1;
}

function coupon_price(){
    return 0.1;
}

function reel_status($num, $need='text'){
    return match($num){
        null | 0 => ($need=='text') ? 'Processing' : '#666666',
        1 => ($need=='text') ? 'Played' : '#53B175',
        2 => ($need=='text') ? 'Paused' : '#CDB229',
        3 => ($need=='text') ? 'Finished' : '#D72120',
    };
}

function pusher($body, $event='my-event', $channel='my-channel'){
    $pusher = new Pusher(env('PUSHER_APP_KEY'), env('PUSHER_APP_SECRET'), env('PUSHER_APP_ID'), [
        'cluster' => env('PUSHER_APP_CLUSTER'),
        'encrypted' => true
    ]);

    $pusher->trigger($channel, $event, $body);
}