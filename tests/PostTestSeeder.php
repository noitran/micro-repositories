<?php

declare(strict_types=1);

namespace Noitran\Repositories\Tests;

use Faker\Generator as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Noitran\Repositories\Tests\Stubs\Models\Comment;
use Noitran\Repositories\Tests\Stubs\Models\Post;
use Noitran\Repositories\Tests\Stubs\Models\Tag;
use Noitran\Repositories\Tests\Stubs\Models\User;

class PostTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /** @var Faker $faker */
        $faker = app(Faker::class);

        /** @var array $users */
        $users = factory(User::class, 5)->create()->map(function (User $person) {
            return $person->getKey();
        })->all();

        $user = function () use ($faker, $users) {
            return $faker->randomElement($users);
        };

        /** @var Collection $tags */
        $tags = factory(Tag::class, 10)->create();

        factory(Post::class, 50)->create([
            'author_id' => $user,
        ])->each(function (Post $post) use ($faker, $user, $tags): void {
            factory(Comment::class, $faker->numberBetween(1, 10))->create([
                'post_id' => $post->getKey(),
                'user_id' => $user,
            ]);

            $post->tags()->save($tags->random());
        });
    }
}
