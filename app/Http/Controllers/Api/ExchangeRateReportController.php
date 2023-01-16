<?php

namespace App\Http\Controllers\Api;

use App\Enums\ReportInterval;
use App\Enums\ReportStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\ExchangeRateReportRequest;
use App\Http\Resources\ExchangeRateReportResponse;
use App\Models\ExchangeRate;
use App\Models\ExchangeRateReport;

class ExchangeRateReportController extends Controller
{
    public function index()
    {
        $reports = ExchangeRateReport::query()
            ->where('user_id', auth()->id())
            ->get();

        if ($reports->isEmpty()) {
            return response()->noContent();
        }

        return ExchangeRateReportResponse::collection($reports);
    }

    public function show(ExchangeRateReport $report)
    {
        abort_unless($report->status === ReportStatus::COMPLETE, 404);

        $rates = ExchangeRate::query()
            ->whereIn('currency', $report->currencies)
            ->whereBetween('date', [$report->from, $report->to])
            ->latest('date')
            ->get()
            ->map(function (ExchangeRate $exchangeRate) {
                return [
                    'currency' => $exchangeRate->currency,
                    'date' => $exchangeRate->date,
                    'rate' => $exchangeRate->rate,
                ];
            })
            ->when($report->interval === ReportInterval::MONTHLY, function ($collection) {
                return $collection->groupBy(function ($exchangeRate) {
                    return $exchangeRate['date']->format('Y-m');
                })->map(function ($group) {
                    return $group->groupBy(function ($exchangeRate) {
                        return $exchangeRate['date']->format('Y-m-d');
                    });
                });
            })
            ->when($report->interval === ReportInterval::WEEKLY, function ($collection) {
                return $collection->groupBy(function ($exchangeRate) {
                    return $exchangeRate['date']->format('W');
                })->map(function ($group) {
                    return $group->groupBy(function ($exchangeRate) {
                        return $exchangeRate['date']->format('Y-m-d');
                    });
                });
            })
            ->when($report->interval === ReportInterval::DAILY, function ($collection) {
                return $collection->groupBy(function ($exchangeRate) {
                    return $exchangeRate['date']->format('Y-m-d');
                });
            });

        return ExchangeRateReportResponse::make($report)->additional(['rates' => $rates])->resolve();
    }

    public function store(ExchangeRateReportRequest $request)
    {
        $report = ExchangeRateReport::create([
            'currencies' => $request->currencies,
            'interval' => $request->interval,
            'from' => $request->from,
            'to' => $request->to,
            'status' => ReportStatus::PENDING,
        ]);

        // ideally ProcessExchangeRateReport::dispatch($report); to save having to wait for the 15min command

        return ExchangeRateReportResponse::make($report);
    }
}
