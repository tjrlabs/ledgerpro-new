<?php

namespace App\DTO;
use App\Classes\ResponseData;

Interface BaseDTOInterface {
    public static function from(array $data): ResponseData|self;
    public function validate(): ResponseData|self;
    public function rules(): array;
}
