<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(App\Package::class, function (Faker $faker)
{
    return
    [
       'title' => $faker->text(40),
       'name' => $faker->name,
       'description' => $faker->text(200),
       'height' => $faker->numberBetween(10,400),
       'width' => $faker->numberBetween(10,400),
       'length' => $faker->numberBetween(10,400),
       'weight' => $faker->numberBetween(2,100),
       'photo' => 'public/test.png',
       'email' => $faker->email,
       'phone_number' => '0654321234',
       'postcode_a' => $faker->postcode,
       'postcode_b' => $faker->postcode,
       'avg_confirmed' => 'true',
       'show_hash' => 'aksjdaifgahfgajskhfgasjhgkjg'
    ];
});
