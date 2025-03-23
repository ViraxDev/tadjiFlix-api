<?php

declare(strict_types=1);

namespace App\Exception;

use App\Enum\Exception\ErrorCodeEnum;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

interface ApiExceptionInterface extends HttpExceptionInterface
{
    public function getErrorCodeEnum(): ErrorCodeEnum;

    public function getDetails(): mixed;

    public function getErrorCode(): string;

    public function getStatusCode(): int;
}
