# Migrate DB

<img src="https://preview.dragon-code.pro/TheDragonCode/migrate-db.svg?brand=laravel" alt="Migrate DB"/>

[![Stable Version][badge_stable]][link_packagist]
[![Unstable Version][badge_unstable]][link_packagist]
[![Total Downloads][badge_downloads]][link_packagist]
[![Github Workflow Status][badge_build]][link_build]
[![License][badge_license]][link_license]

> Easy data transfer from one database to another

## Installation

To get the latest version of `Migrate DB`, simply require the project using [Composer](https://getcomposer.org):

```bash
$ composer require dragon-code/migrate-db --dev
```

Or manually update `require-dev` block of `composer.json` and run `composer update`.

```json
{
    "require-dev": {
        "dragon-code/migrate-db": "^2.0"
    }
}
```

### Upgrade from `andrey-helldar/migrate-db`

1. In your `composer.json` file, replace `"andrey-helldar/migrate-db": "^1.0"` with `"dragon-code/migrate-db": "^2.0"`.
2. Run the `command composer` update.
3. Profit!

## Compatibility

| Service | Versions |
|:---|:---|
| PHP | ^7.3, ^8.0 |
| Laravel | ^8.0 |
| Databases | MySQL 5.7+, PostgreSQL 9.5+, MSSQL |

## Using

Create a new database and set up both connections in the `connections` section of
the [config/database.php](https://github.com/laravel/laravel/blob/master/config/database.php) file, then run the `db:migrate` console command passing two
parameters:

```bash
$ php artisan db:migrate --schema-from=foo --schema-to=bar
```

where:

* `foo` - Source [connection](https://github.com/laravel/laravel/blob/master/config/database.php) name
* `bar` - Target [connection](https://github.com/laravel/laravel/blob/master/config/database.php) name

The command will perform all migrations on the source and destination databases and transfer all records from the old to the new one.

Enjoy 😊


## License

This package is licensed under the [MIT License](LICENSE).


[badge_build]:          https://img.shields.io/github/workflow/status/dragon-code/migrate-db/phpunit?style=flat-square

[badge_downloads]:      https://img.shields.io/packagist/dt/dragon-code/migrate-db.svg?style=flat-square

[badge_license]:        https://img.shields.io/packagist/l/dragon-code/migrate-db.svg?style=flat-square

[badge_stable]:         https://img.shields.io/github/v/release/dragon-code/migrate-db?label=stable&style=flat-square

[badge_unstable]:       https://img.shields.io/badge/unstable-dev--main-orange?style=flat-square

[link_build]:           https://github.com/dragon-code/migrate-db/actions

[link_license]:         LICENSE

[link_packagist]:       https://packagist.org/packages/dragon-code/migrate-db
