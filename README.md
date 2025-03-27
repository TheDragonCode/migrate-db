# Migrate DB for Laravel

<img src="https://preview.dragon-code.pro/the-dragon-code/migrate-db.svg?brand=laravel" alt="Migrate DB"/>

[![Stable Version][badge_stable]][link_packagist]
[![Total Downloads][badge_downloads]][link_packagist]
[![License][badge_license]][link_license]

> Easy data transfer from one database to another

## Installation

To get the latest version of `Migrate DB`, simply require the project using [Composer](https://getcomposer.org):

```bash
composer require dragon-code/migrate-db --dev
```

Or manually update `require-dev` block of `composer.json` and run `composer update`.

```json
{
    "require-dev": {
        "dragon-code/migrate-db": "^3.0"
    }
}
```

## Compatibility

| Service   | Versions                           |
|:----------|:-----------------------------------|
| PHP       | ^8.0                               |
| Laravel   | ^8.0, ^9.0, ^10.0, ^11.0, ^12.0    |
| Databases | MySQL 5.7+, PostgreSQL 9.5+, MSSQL |

| Laravel \ PostgreSQL | 9  | 10 | 11 | 12 | 13 | 14 | 15 | 16 | 17 |
|:---------------------|----|----|----|----|----|----|----|----|----|
| 8                    | ✅  | ✅  | ✅  | ✅  | ✅  | ✅  | ✅  | ✅  | ✅  |
| 9                    | ✅  | ✅  | ✅  | ✅  | ✅  | ✅  | ✅  | ✅  | ✅  |
| 10                   | ✅  | ✅  | ✅  | ✅  | ✅  | ✅  | ✅  | ✅  | ✅  |
| 11                   | ✖️ | ✖️ | ✖️ | ✅  | ✅  | ✅  | ✅  | ✅  | ✅  |
| 12                   | ✖️ | ✖️ | ✖️ | ✅  | ✅  | ✅  | ✅  | ✅  | ✅  |


## Usage

Create a new database and set up both connections in the `connections` section of
the [config/database.php](https://github.com/laravel/laravel/blob/master/config/database.php) file, then run the `db:migrate` console command passing two
parameters:

```bash
php artisan db:migrate --schema-from=foo --schema-to=bar
```

### For Certain Tables

```bash
php artisan db:migrate --schema-from=foo --schema-to=bar --tables=table1 --tables=table2
```

### Exclude Certain Tables

```bash
php artisan db:migrate --schema-from=foo --schema-to=bar --exclude=table1 --exclude=table2
```

where:

* `foo` - Source [connection](https://github.com/laravel/laravel/blob/master/config/database.php) name
* `bar` - Target [connection](https://github.com/laravel/laravel/blob/master/config/database.php) name

Follow on screen instructions and then command will perform all migrations on the source and destination databases and transfer all records from the old to the new one.

Enjoy 😊


## License

This package is licensed under the [MIT License](LICENSE).


[badge_downloads]:      https://img.shields.io/packagist/dt/dragon-code/migrate-db.svg?style=flat-square

[badge_license]:        https://img.shields.io/packagist/l/dragon-code/migrate-db.svg?style=flat-square

[badge_stable]:         https://img.shields.io/github/v/release/TheDragonCode/migrate-db?label=stable&style=flat-square

[link_build]:           https://github.com/TheDragonCode/migrate-db/actions

[link_license]:         LICENSE

[link_packagist]:       https://packagist.org/packages/dragon-code/migrate-db
