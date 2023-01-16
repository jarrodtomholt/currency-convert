<?php

namespace Database\Factories;

use App\Enums\ReportInterval;
use App\Enums\ReportStatus;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExchangeRateReport>
 */
class ExchangeRateReportFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'currencies' => Currency::factory()->count(rand(1, 5))->create()->pluck('code'),
            'interval' => ReportInterval::MONTHLY,
            'from' => now()->subYear(),
            'to' => now(),
            'status' => ReportStatus::PENDING,
        ];
    }

    public function complete(): Factory
    {
        return $this->state(function () {
            return [
                'status' => ReportStatus::COMPLETE,
            ];
        });
    }
}
