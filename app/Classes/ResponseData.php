<?php

namespace App\Classes;

class ResponseData
{
    public array $data;

    protected array $errors;

    public function __construct(array|self $data = [], array $errors = [])
    {
        if ($data instanceof self) {
            $this->data = $data->getData();
            $this->errors = $data->getErrorMessages();

            return;
        }

        $this->data = $data;
        $this->errors = $errors;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getErrorMessages(): array
    {
        return $this->errors;
    }
}