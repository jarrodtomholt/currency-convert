<?php

namespace Tests\Feature;

use App\Console\Commands\ExchangeRateReportScheduleCommand;
use App\Enums\ReportStatus;
use App\Jobs\ProcessExchangeRateReport;
use App\Models\ExchangeRate;
use App\Models\ExchangeRateReport;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ExchangeRateCommandTest extends TestCase
{
    public function testItDoesNotDispatchAJobWhenThereAreNoPendingReportRequests()
    {
        Queue::fake();

        $this->artisan(ExchangeRateReportScheduleCommand::class);

        Queue::assertNothingPushed();
    }

    public function testProcessExchangeRateReportJobsAreDispatchedWhenTheCommandRuns()
    {
        Queue::fake();

        $report = ExchangeRateReport::factory()->create();

        $this->artisan(ExchangeRateReportScheduleCommand::class);

        Queue::assertPushed(ProcessExchangeRateReport::class, 1);

        Queue::assertPushedOn(
            'http',
            ProcessExchangeRateReport::class,
            function (ProcessExchangeRateReport $job) use ($report) {
                return $job->report->is($report);
            });
    }

    public function testSuccessfullyProcessingTheReportUpdatesStatus()
    {
        Http::fake([
            'timeframe*' => Http::response(
                [
                    'quotes' => [
                        '2022-11-01' => [
                            'USDAUD' => 1.575659,
                            'USDEUR' => 1.01845,
                            'USDJPY' => 147.577968,
                        ],
                    ],
                ],
                Response::HTTP_OK
            ),
        ]);

        $report = ExchangeRateReport::factory()->create([
            'currencies' => ['AUD', 'EUR', 'JPY'],
        ]);

        $this->artisan(ExchangeRateReportScheduleCommand::class);

        $this->assertTrue($report->fresh()->status === ReportStatus::COMPLETE);
    }

    public function testOnlyInsertsExchangeRateDataThatDoesNotAlreadyExistForCurrencyAndDateCombinations()
    {
        $apiResponse = File::get(base_path('tests/stubs/apilayer-timeframe-response.json'));

        Http::fake([
            'timeframe*' => Http::response(
                [
                    'quotes' => [
                        '2022-11-01' => [
                            'USDAUD' => 1.575659,
                            'USDEUR' => 1.01845,
                            'USDJPY' => 147.577968,
                        ],
                    ],
                ],
                Response::HTTP_OK
            ),
        ]);

        ExchangeRate::factory()->create([
            'currency' => 'AUD',
            'date' => Carbon::parse('2022-11-01'),
        ]);

        ExchangeRateReport::factory()->create([
            'currencies' => ['AUD', 'EUR', 'JPY'],
        ]);

        $this->artisan(ExchangeRateReportScheduleCommand::class);

        $this->assertDatabaseCount(ExchangeRate::class, 3);
    }
}
