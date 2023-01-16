<?php

namespace App\Http\Requests;

use App\Enums\ReportInterval;
use App\Enums\ReportRange;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class ExchangeRateReportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'currencies' => ['required', 'array', 'exists:App\Models\Currency,code'],
            'range' => ['required', new Enum(ReportRange::class)],
            'interval' => []
        ];
    }

    public function passedValidation(): void
    {
        [$interval, $from, $to] = match ($this->range) {
            'Last 12 Months' => [ReportInterval::MONTHLY, now()->subYear(), now()],
            'Last 6 Months' => [ReportInterval::WEEKLY, now()->subMonths(6), now()],
            'Last Month' => [ReportInterval::DAILY, now()->subMonth(), now()],
        };

        $this->merge([
            'interval' => $interval,
            'from' => $from,
            'to' => $to,
        ]);
    }
}
