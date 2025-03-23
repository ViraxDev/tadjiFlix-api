<?php

declare(strict_types=1);

namespace App\Exception;

use App\Enum\Exception\ErrorCodeEnum;
use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class AbstractApiException extends HttpException implements ApiExceptionInterface
{
    public function __construct(private readonly mixed $details = null)
    {
        parent::__construct($this->getErrorCodeEnum()->getHttpStatusCode(), $this->getErrorCodeEnum()->getMessage());
    }

    abstract public function getErrorCodeEnum(): ErrorCodeEnum;

    public function getDetails(): mixed
    {
        return $this->details;
    }

    public function getErrorCode(): string
    {
        return $this->getErrorCodeEnum()->value;
    }

    public function getStatusCode(): int
    {
        return $this->getErrorCodeEnum()->getHttpStatusCode();
    }
}
