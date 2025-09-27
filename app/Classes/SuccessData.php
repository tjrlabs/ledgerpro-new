<?php

namespace App\Classes;

class SuccessData extends ResponseData
{
    public function __construct(array $data)
    {
        $this->data = $data;
    }
}
