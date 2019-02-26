MicroRepositories
====================

<p align="center">
<a href="https://scrutinizer-ci.com/g/noitran/micro-repositories/code-structure"><img src="https://img.shields.io/scrutinizer/coverage/g/noitran/micro-repositories.svg?style=flat-square" alt="Coverage Status"></img></a>
<a href="https://scrutinizer-ci.com/g/noitran/micro-repositories"><img src="https://img.shields.io/scrutinizer/g/noitran/micro-repositories.svg?style=flat-square" alt="Quality Score"></img></a>
<a href="LICENSE"><img src="https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square" alt="Software License"></img></a>
<a href="https://github.com/noitran/micro-repositories/releases"><img src="https://img.shields.io/github/release/noitran/micro-repositories.svg?style=flat-square" alt="Latest Version"></img></a>
<a href="https://packagist.org/packages/noitran/micro-repositories"><img src="https://img.shields.io/packagist/dt/noitran/micro-repositories.svg?style=flat-square" alt="Total Downloads"></img></a>
</p>

## About

* Package adds support to work with Repository Design pattern in laravel and lumen applications. Package was created from scratch as other versions of repository pattern packages were unmaintained or poor quality. Contributions are welcome!

## Install

* Install as composer package

```bash
$ composer require noitran/micro-repositories
```

#### Laravel

* Laravel uses provider auto discovery. Config file can be published using command

```
$ artisan vendor:publish --provider="Noitran\Repositories\ServiceProvider"
```

#### Lumen

* Open your bootstrap/app.php and register as service provider

```php
$app->register(Noitran\Repositories\ServiceProvider::class);
```

* Config file should be loaded manually in bootstrap/app.php

```php
$app->configure('repositories');
```

## Repositories

#### Creating Eloquent model

* Create Eloquent model like you do it initially. Using laravel generators or manually. Example model what I will use:

```php
<?php

namespace App\Data\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id',
    ];
}
```

#### Creating Eloquent Repository implementation and Repository Interface

* First let's create Interface of our Repository

```
<?php

namespace App\Data\Repositories\User;

use Noitran\Repositories\Contracts\Repository\RepositoryInterface;

/**
 * Interface UserRepository
 */
interface UserRepository extends RepositoryInterface
{
    //
}
```

* Second step will be to create implementation of Repository Interface. We will extend `SqlRepository` which adds support for querying with table names. If you use `jenssegers/laravel-mongodb` package, then your repository implementation should extend `MongoRepository` class

```php
<?php

namespace App\Data\Repositories\User;

use Noitran\Repositories\Repositories\SqlRepository;
use App\Data\Models\User;

/**
 * Class UserRepositoryEloquent
 */
class UserRepositoryEloquent extends SqlRepository implements UserRepository
{
    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function getModelClassName(): string
    {
        return User::class;
    }

    /**
     * Boot up the repository, pushing criteria.
     */
    public function boot(): void
    {
        //
    }
}
```

* After Repository and Interface were created, they should be loaded using laravel IoC binding. Fastest way to do it is to add binding into `App\Providers\AppServiceProvider` class.

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Data\Repositories\User\UserRepository;
use App\Data\Repositories\User\UserRepositoryEloquent;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(UserRepository::class, UserRepositoryEloquent::class);
    }
}
```
