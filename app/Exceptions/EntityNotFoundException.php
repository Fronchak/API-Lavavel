<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EntityNotFoundException extends ApiException
{
    public function __construct(string $message) {
        parent::__construct($message, 404, 'Entity not found');
    }
}
