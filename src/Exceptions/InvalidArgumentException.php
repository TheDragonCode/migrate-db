<?php

namespace Helldar\MigrateDB\Exceptions;

use Symfony\Component\Console\Exception\InvalidArgumentException as BaseException;

final class InvalidArgumentException extends BaseException
{
    public function __construct(string $key)
    {
        parent::__construct('The "' . $key . '" option does not exist.');
    }
}
