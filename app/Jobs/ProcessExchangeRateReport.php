<?php

namespace App\Jobs;

use App\Enums\ReportStatus;
use App\Models\ExchangeRate;
use App\Models\ExchangeRateReport;
use App\Services\ExchangeRateApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessExchangeRateReport implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public ExchangeRateReport $report;

    private Collection $existing;

    public function __construct(ExchangeRateReport $report)
    {
        $this->onQueue('http');
        $this->report = $report;
    }

    public function handle(ExchangeRateApiService $api)
    {
        $this->existing = ExchangeRate::query()
            ->whereIn('currency', $this->report->currencies)
            ->whereBetween('date', [$this->report->from, $this->report->to])
            ->get();

        $api->timeframe($this->report->currencies, $this->report->from, $this->report->to)
            ->reject(function ($exchangeRate) {
                return $this->existing
                    ->where('currency', $exchangeRate->currency)
                    ->where('date', $exchangeRate->date)
                    ->isNotEmpty();
            })
            ->each(function ($exchangeRate) {
                ExchangeRate::create([
                    'currency' => $exchangeRate->currency,
                    'date' => $exchangeRate->date,
                    'rate' => $exchangeRate->rate,
                ]);
            });

        $this->report->update([
            'status' => ReportStatus::COMPLETE,
        ]);
    }

    public function uniqueId()
    {
        return $this->report->id;
    }
}
