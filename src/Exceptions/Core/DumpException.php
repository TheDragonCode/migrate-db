<?php

namespace Helldar\MoveDb\Exceptions\Core;

use Exception;

final class DumpException extends Exception
{
    public function __construct(string $value)
    {
        parent::__construct($value, 500);
    }
}
