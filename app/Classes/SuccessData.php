<?php

namespace App\Classes;

class SuccessData extends ResponseData
{
    public function __construct(array $data = [], ?string $message = null)
    {
        if ($message !== null && ! array_key_exists('message', $data)) {
            $data['message'] = $message;
        }

        parent::__construct($data);
    }
}