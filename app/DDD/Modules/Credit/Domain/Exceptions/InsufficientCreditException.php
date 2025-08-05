<?php

namespace App\DDD\Modules\Credit\Domain\Exceptions;

use Exception;

class InsufficientCreditException extends Exception
{
    public function __construct(string $message = "Yetersiz kredi bakiyesi", int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
} 