<?php

namespace Tests\Feature;

use App\Enums\ReportInterval;
use App\Enums\ReportRange;
use App\Enums\ReportStatus;
use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Models\ExchangeRateReport;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Carbon;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ExchangeRateReportControllerTest extends TestCase
{
    private $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function testItReturnsAListOfAUsersCurrentReports()
    {
        ExchangeRateReport::factory()->create([
            'user_id' => 1,
        ]);

        $this->getJson(route('api.exchange-rate-reports.index'))
            ->assertJson(function (AssertableJson $json) {
                $json->has('0.currencies')
                    ->has('0.from')
                    ->has('0.to')
                    ->etc();
            });
    }

    /** @dataProvider newReportDataProvider */
    public function testItValidatesFieldsToCreateANewPendingReport($field, $input)
    {
        $this->postJson(route('api.exchange-rate-reports.store', [
            $field => $input,
        ]))->assertUnprocessable()->assertJsonValidationErrorFor($field);
    }

    private function newReportDataProvider(): array
    {
        return [
            ['currencies', ''],
            ['currencies', []],
            ['currencies', ['AUD']],
            ['range', ''],
            ['range', 'Foo'],
        ];
    }

    public function testItCreatesANewPendingReport()
    {
        $currency = Currency::factory()->create();

        $this->postJson(route('api.exchange-rate-reports.store', [
            'currencies' => [$currency->code],
            'range' => 'Last 12 Months',
        ]))->assertSuccessful();

        $this->assertDatabaseHas(ExchangeRateReport::class, [
            'currencies' => json_encode([$currency->code]),
            'status' => ReportStatus::PENDING,
        ]);
    }

    /** @dataProvider reportRangeToIntervalDataProvider */
    public function testItTransformsReportRangesToIntervalOnCreate($ruleset)
    {
        $currency = Currency::factory()->create();

        $this->postJson(route('api.exchange-rate-reports.store', [
            'currencies' => [$currency->code],
            'range' => $ruleset['range'],
        ]))->assertSuccessful();

        $this->assertDatabaseHas(ExchangeRateReport::class, [
            'currencies' => json_encode([$currency->code]),
            'interval' => $ruleset['interval'],
            'from' => $ruleset['from'],
            'to' => $ruleset['to'],
        ]);
    }

    private function reportRangeToIntervalDataProvider(): array
    {
        return [
            [
                [
                    'range' => ReportRange::LAST12MONTHS,
                    'interval' => ReportInterval::MONTHLY,
                    'from' => now()->subYear()->startOfDay()->format('Y-m-d H:i:s'),
                    'to' => now()->endOfDay()->format('Y-m-d H:i:s'),
                ],
            ],
            [
                [
                    'range' => ReportRange::LAST6MONTHS,
                    'interval' => ReportInterval::WEEKLY,
                    'from' => now()->subMonths(6)->startOfDay()->format('Y-m-d H:i:s'),
                    'to' => now()->endOfDay()->format('Y-m-d H:i:s'),
                ],
            ],
            [
                [
                    'range' => ReportRange::LASTMONTH,
                    'interval' => ReportInterval::DAILY,
                    'from' => now()->subMonth()->startOfDay()->format('Y-m-d H:i:s'),
                    'to' => now()->endOfDay()->format('Y-m-d H:i:s'),
                ],
            ],
        ];
    }

    public function testItReturnsANotFoundStatusWhenReportStatusIsPending()
    {
        $report = ExchangeRateReport::factory()
            ->for($this->user)
            ->create();

        $this->getJson(route('api.exchange-rate-reports.show', [
            'report' => $report,
        ]))->assertNotFound();
    }

    public function testItReturnsTheReportWhenStatusIsNotPending()
    {
        $report = ExchangeRateReport::factory()
            ->for($this->user)
            ->complete()
            ->create();

        $this->getJson(route('api.exchange-rate-reports.show', [
            'report' => $report,
        ]))->assertSuccessful();
    }

    public function testItReturnsReportDataForACompletedReport()
    {
        Carbon::setTestNow('2022-11-05 17:27:18');

        $report = ExchangeRateReport::factory()
            ->for($this->user)
            ->complete()
            ->create([
                'currencies' => ['AUD', 'EUR', 'JPY'],
                'from' => now()->subDays(3),
            ]);

        ExchangeRate::factory()->count(12)->state(new Sequence(
            ['currency' => 'AUD', 'date' => now()->subDays(3), 'rate' => 0.123],
            ['currency' => 'AUD', 'date' => now()->subDays(2), 'rate' => 0.122],
            ['currency' => 'AUD', 'date' => now()->subDays(1), 'rate' => 0.121],
            ['currency' => 'AUD', 'date' => now(), 'rate' => 0.999],
            ['currency' => 'EUR', 'date' => now()->subDays(3), 'rate' => 1.23],
            ['currency' => 'EUR', 'date' => now()->subDays(2), 'rate' => 1.22],
            ['currency' => 'EUR', 'date' => now()->subDays(1), 'rate' => 1.21],
            ['currency' => 'EUR', 'date' => now(), 'rate' => 9.99],
            ['currency' => 'JPY', 'date' => now()->subDays(3), 'rate' => 123.3],
            ['currency' => 'JPY', 'date' => now()->subDays(2), 'rate' => 122.2],
            ['currency' => 'JPY', 'date' => now()->subDays(1), 'rate' => 111.1],
            ['currency' => 'JPY', 'date' => now(), 'rate' => 999.99],
        ))->create();

        $this->getJson(route('api.exchange-rate-reports.show', [
            'report' => $report,
        ]))->assertJson(function (AssertableJson $json) {
            $json->where('currencies', ['AUD', 'EUR', 'JPY'])
                ->has('from')
                ->has('to')
                ->has('rates')
                ->etc();
        });
    }

    public function testItDoesNotAuthoriseViewOfAReportRequestedByAnotherUser()
    {
        $this->withoutExceptionHandling()
            ->expectException(AuthorizationException::class);

        $report = ExchangeRateReport::factory()
            ->for(User::factory())
            ->complete()
            ->create();

        $this->getJson(route('api.exchange-rate-reports.show', [
            'report' => $report,
        ]))->dd();
    }
}
