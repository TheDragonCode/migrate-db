<?php

namespace Helldar\MigrateDB\Exceptions;

use Symfony\Component\Console\Exception\InvalidArgumentException as BaseException;

final class UnknownDatabaseDriverException extends BaseException
{
    public function __construct(string $driver)
    {
        parent::__construct('Database connection [' . $driver . '] not configured.');
    }
}
