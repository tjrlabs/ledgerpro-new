<?php

namespace App\Classes;

class ErrorData extends ResponseData
{
    public function __construct(array $errorMessages)
    {
        $this->errorMessages = $errorMessages;
    }

}
