<?php

use Pusher\Pusher;

function generateRandomCoupon($length = 7) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $coupon = '';
    for ($i = 0; $i < $length; $i++) {
        $coupon .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $coupon;
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