<?php

namespace App\DTO\Reports;

use App\Classes\ErrorData;
use App\DTO\BaseDTOInterface;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ProfitLossDTO implements BaseDTOInterface
{
    public function __construct(
        public string $month,
        public string $year,
        public ?string $boardMonthYear = null,
    ) {
        if (!$this->boardMonthYear) {
            $this->boardMonthYear = sprintf('%02d-%s', (int) $this->month, $this->year);
        }
    }

    public static function from(array $data): ErrorData|ProfitLossDTO
    {
        $validator = Validator::make($data, [
            'month' => 'required|numeric|min:1|max:12',
            'year' => 'required|numeric|min:2020|max:2035',
        ], [
            'month.required' => 'Please select a month.',
            'month.min' => 'Month must be between 1 and 12.',
            'month.max' => 'Month must be between 1 and 12.',
            'year.required' => 'Please select a year.',
        ]);

        if ($validator->fails()) {
            return new ErrorData($validator->errors()->all());
        }

        return new self(
            (string) $data['month'],
            (string) $data['year'],
            sprintf('%02d-%s', (int) $data['month'], $data['year'])
        );
    }

    public function rules(): array
    {
        return [
            'month' => 'required|numeric|min:1|max:12',
            'year' => 'required|numeric|min:2020|max:2035',
        ];
    }

    public function validate(): ErrorData|ProfitLossDTO
    {
        $validator = Validator::make([
            'month' => $this->month,
            'year' => $this->year,
        ], $this->rules(), [
            'month.required' => 'Please select a month.',
            'month.min' => 'Month must be between 1 and 12.',
            'month.max' => 'Month must be between 1 and 12.',
            'year.required' => 'Please select a year.',
        ]);

        if ($validator->fails()) {
            return new ErrorData($validator->errors()->all());
        }

        return $this;
    }

    public function toArray(): array
    {
        return [
            'board_month_year' => $this->boardMonthYear,
        ];
    }

    public function getFormattedMonthYear(): string
    {
        return Carbon::createFromFormat('m-Y', $this->boardMonthYear)->format('F Y');
    }
}
