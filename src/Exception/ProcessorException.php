<?php

namespace App\Exception;

class ProcessorException extends \Exception
{

    public function __construct(string $message = "Bad Request", int $code = 500, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}