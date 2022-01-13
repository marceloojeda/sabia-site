<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Sale;
use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\Config;

$factory->define(Sale::class, function (Faker $faker) {
    $arrGender = ['male', 'female'];
    $arrPaymentMethod = Config::get('constants.PAYMENT_METHOD');
    $arrPaymentStatus = Config::get('constants.PAYMENT_STATUS');
    $arrReturn = [
        'is_ecommerce' => rand(0, 1),
        'payment_method' => $arrPaymentMethod[array_rand($arrPaymentMethod)],
        'payment_status' => 'Pago', //$arrPaymentStatus[array_rand($arrPaymentStatus)],
        'amount' => 12,
        'amount_paid' => 0,
        'buyer' => $faker->name($arrGender[array_rand($arrGender)]),
        'buyer_email' => $faker->email(),
        'buyer_phone' => $faker->phoneNumber(),
        'ticket_number' => null,
        'payment_date' => null
    ];

    if(!$arrReturn['payment_status'] || $arrReturn['payment_status'] === 'Pago') {
        $arrReturn['payment_status'] = 'Pago';
        $arrReturn['amount_paid'] = 12;
        $arrReturn['ticket_number'] = random_int(1, 2200);
        $arrReturn['payment_date'] = date('Y-m-d H:i:s');
    }

    return $arrReturn;
});
