<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

$factory->define(\App\Models\Loan::class, function (\Faker\Generator $faker) {
    return [
        'amount' => $faker->numberBetween(1000, 100000),
        'term' => $faker->numberBetween(6, 60),
        'status' => $faker->numberBetween(1, 3),
    ];
});
