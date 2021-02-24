<?php

namespace Helldar\MigrateDB\Exceptions;

use Symfony\Component\Console\Exception\InvalidArgumentException as BaseException;

final class InvalidArgumentException extends BaseException
{
    public function __construct(string $driver)
    {
        parent::__construct('The "' . $driver . '" option does not exist.');
    }
}
