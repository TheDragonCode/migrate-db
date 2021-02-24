# Migrate DB

Easy data transfer from one database to another

[![Stable Version][badge_stable]][link_packagist]
[![Unstable Version][badge_unstable]][link_packagist]
[![Total Downloads][badge_downloads]][link_packagist]
[![License][badge_license]][link_license]

## Table of contents

* [Installation](#installation)
* [Using](#using)

## Installation

To get the latest version of `Migrate DB`, simply require the project using [Composer](https://getcomposer.org):

```bash
$ composer require andrey-helldar/migrate-db
```

Or manually update `require-dev` block of `composer.json` and run `composer update`.

```json
{
    "require-dev": {
        "andrey-helldar/migrate-db": "^1.0"
    }
}
```

## Using

Create a new database and set up both connections in the `connections` section of
the [config/database.php](https://github.com/laravel/laravel/blob/8.x/config/database.php) file, then run the `db:migrate` console command passing two
parameters:

```bash
$ php artisan db:migrate --schema-from=source --schema-to=target
```

where:

>    `source` - Source connection name
>
>    `target` - Target connection name

For example:

```bash
$ php artisan db:migrate --schema-from=foo --schema-to=bar
```

The command will perform all migrations on the source and destination databases and transfer all records from the old to the new one.

Enjoy 😊

[badge_downloads]:      https://img.shields.io/packagist/dt/andrey-helldar/migrate-db.svg?style=flat-square

[badge_license]:        https://img.shields.io/packagist/l/andrey-helldar/migrate-db.svg?style=flat-square

[badge_stable]:         https://img.shields.io/github/v/release/andrey-helldar/migrate-db?label=stable&style=flat-square

[badge_unstable]:       https://img.shields.io/badge/unstable-dev--main-orange?style=flat-square

[link_license]:         LICENSE

[link_packagist]:       https://packagist.org/packages/andrey-helldar/migrate-db
