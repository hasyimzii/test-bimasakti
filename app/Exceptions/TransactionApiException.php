<?php

namespace App\Exceptions;

use Exception;

class TransactionApiException extends Exception
{
    public function __construct(
        public readonly string $userMessage,
        public readonly int $statusCode = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($userMessage, $statusCode, $previous);
    }
}
