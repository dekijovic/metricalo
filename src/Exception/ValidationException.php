<?php

namespace App\Exception;

class ValidationException extends \Exception
{

    public function __construct(private readonly array $data, string $message = "Validation Failed", int $code = 422, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getData()
    {
        return $this->data;
    }

}