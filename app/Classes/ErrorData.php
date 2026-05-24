<?php

namespace App\Classes;

class ErrorData extends ResponseData
{
    public function __construct(array $errors = [], array $data = [])
    {
        parent::__construct($data, $errors);
    }
}