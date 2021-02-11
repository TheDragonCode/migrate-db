<?php

namespace Helldar\MoveDb\Exceptions\Http;

use Symfony\Component\HttpKernel\Exception\HttpException;

final class DumpHttpException extends HttpException
{
    public function __construct(string $value)
    {
        parent::__construct(400, $value);
    }
}
