# Migrate DB

Easy data transfer from one database to another

<p align="center">
    <img src="/.github/images/screenshoot.png?raw=true" alt="Migrate DB"/>
</p>

[![StyleCI Status][badge_styleci]][link_styleci]
[![Github Workflow Status][badge_build]][link_build]
[![Coverage Status][badge_coverage]][link_scrutinizer]
[![Scrutinizer Code Quality][badge_quality]][link_scrutinizer]

[![Stable Version][badge_stable]][link_packagist]
[![Unstable Version][badge_unstable]][link_packagist]
[![Total Downloads][badge_downloads]][link_packagist]
[![License][badge_license]][link_license]

## Installation

To get the latest version of `Migrate DB`, simply require the project using [Composer](https://getcomposer.org):

```bash
$ composer require andrey-helldar/migrate-db --dev
```

Or manually update `require-dev` block of `composer.json` and run `composer update`.

```json
{
    "require-dev": {
        "andrey-helldar/migrate-db": "^1.1"
    }
}
```

## Compatibility

| Service | Versions |
|:---|:---|
| PHP | ^7.3, ^8.0 |
| Laravel | ^8.0 |
| Databases | MySQL, PostgreSQL, MSSQL |

## Using

Create a new database and set up both connections in the `connections` section of
the [config/database.php](https://github.com/laravel/laravel/blob/8.x/config/database.php) file, then run the `db:migrate` console command passing two
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


## For Enterprise

Available as part of the Tidelift Subscription.

The maintainers of `andrey-helldar/migrate-db` and thousands of other packages are working with Tidelift to deliver commercial support and maintenance for the open source packages you use to build your applications. Save time, reduce risk, and improve code health, while paying the maintainers of the exact packages you use. [Learn more](https://tidelift.com/subscription/pkg/packagist-andrey-helldar-migrate-db?utm_source=packagist-andrey-helldar-migrate-db&utm_medium=referral&utm_campaign=enterprise&utm_term=repo).



[badge_build]:          https://img.shields.io/github/workflow/status/andrey-helldar/migrate-db/phpunit?style=flat-square

[badge_coverage]:       https://img.shields.io/scrutinizer/coverage/g/andrey-helldar/migrate-db.svg?style=flat-square

[badge_downloads]:      https://img.shields.io/packagist/dt/andrey-helldar/migrate-db.svg?style=flat-square

[badge_license]:        https://img.shields.io/packagist/l/andrey-helldar/migrate-db.svg?style=flat-square

[badge_quality]:        https://img.shields.io/scrutinizer/g/andrey-helldar/migrate-db.svg?style=flat-square

[badge_stable]:         https://img.shields.io/github/v/release/andrey-helldar/migrate-db?label=stable&style=flat-square

[badge_styleci]:        https://styleci.io/repos/338000763/shield

[badge_unstable]:       https://img.shields.io/badge/unstable-dev--main-orange?style=flat-square

[link_build]:           https://github.com/andrey-helldar/migrate-db/actions

[link_license]:         LICENSE

[link_packagist]:       https://packagist.org/packages/andrey-helldar/migrate-db

[link_scrutinizer]:     https://scrutinizer-ci.com/g/andrey-helldar/migrate-db/?branch=main

[link_styleci]:         https://github.styleci.io/repos/338000763
