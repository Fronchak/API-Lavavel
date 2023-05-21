<?php

namespace App\Exceptions;

use Exception;

class BadRequestException extends ApiException
{
    public function __construct(string $message, string $error = 'Bad request') {
        parent::__construct($message, 400, $error);
    }
}
