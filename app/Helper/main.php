<?php

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
