<?php

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory as EloquentFactory;

/** @var EloquentFactory $factory */

/** User */
$factory->define(Noitran\Repositories\Tests\Stubs\Models\User::class, function (Faker $faker) {
    return [
        'name' => $faker->firstName,
        'surname' => $faker->lastName,
        'email' => $faker->safeEmail,
        'password' => str_random(10),
    ];
});

/** Post */
$factory->define(Noitran\Repositories\Tests\Stubs\Models\Post::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence(3),
        'slug' => $faker->slug(),
        'content' => $faker->paragraphs(3, true),
        'published_at' => $faker->dateTime,
        'author_id' => function () {
            return factory(Noitran\Repositories\Tests\Stubs\Models\User::class)->create()->getKey();
        },
    ];
});

/** Comment */
$factory->define(Noitran\Repositories\Tests\Stubs\Models\Comment::class, function (Faker $faker) {
    return [
        'content' => $faker->paragraph,
        'post_id' => function () {
            return factory(Noitran\Repositories\Tests\Stubs\Models\Post::class)->create()->getKey();
        },
        'user_id' => function () {
            return factory(Noitran\Repositories\Tests\Stubs\Models\User::class)->create()->getKey();
        },
    ];
});

/** Tag */
$factory->define(Noitran\Repositories\Tests\Stubs\Models\Tag::class, function (Faker $faker) {
    return [
        'name' => $faker->country,
    ];
});
