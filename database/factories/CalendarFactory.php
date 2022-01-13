<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Calendar;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\Config;

$factory->define(Calendar::class, function (Faker $faker) {
    $arrType = Config::get('constants.EVENTO_TIPO');
    $arrAudience = Config::get('constants.EVENTO_ALVO');
    $arrStatus = Config::get('constants.EVENTO_STATUS');
    return [

        'title' => $faker->title(),
        'description' => $faker->text(),
        'type' => $arrType[array_rand(Config::get('constants.EVENTO_TIPO'))],
        'audience' => $arrAudience[array_rand(Config::get('constants.EVENTO_ALVO'))],
        'begin_at' => $faker->dateTime(),
        'finish_at' => $faker->dateTime(),
        'status' => $arrStatus[array_rand(Config::get('constants.EVENTO_STATUS'))],
        'is_active' => array_rand([false, true])
    ];
});
