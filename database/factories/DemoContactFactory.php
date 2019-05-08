<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Api\Models\DemoContact::class, function (Faker $faker) {
    return [
        'email'      => $faker->unique()->safeEmail,
        'first_name' => $faker->firstName,
        'last_name'  => $faker->lastName,
        'photo_url'  => $faker->imageURL(),
        'address1'   => $faker->streetAddress,
        'address2'   => $faker->streetName,
        'city'       => $faker->city,
        'state'      => $faker->state,
        'postal'     => $faker->postcode,
        'country'    => $faker->country,
        'phone'      => $faker->phoneNumber,
        'occupation' => $faker->jobTitle,
        'employer'   => $faker->company,
        'note'       => $faker->sentence,
        'lat'        => $faker->latitude,
        'lng'        => $faker->longitude
    ];
});
