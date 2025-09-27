<?php

namespace App\Classes;

class ResponseData
{
    public array $data = [];
    public array $errorMessages = [];
    public bool $error = false;

    public function generateResponse(): array
    {
        return [
            'error' => $this->error,
            'data' => $this->data,
            'errorMessages' => $this->errorMessages[0] ?? '',
        ];
    }

    public function getErrorMessages(): array
    {
        return $this->errorMessages;
    }
}
