<?php

namespace Tests\Feature;

use App\Exceptions\ExchangeRateApiException;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ExchangeRateControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Sanctum::actingAs(User::factory()->create());

        Http::preventStrayRequests();
    }

    /** @dataProvider exchangeRateRequestDataProvider */
    public function testItValidatesFieldsToCreateANewPendingReport($field, $input)
    {
        $this->postJson(route('api.exchange-rates', [
            $field => $input,
        ]))->assertUnprocessable()->assertJsonValidationErrorFor($field);
    }

    private function exchangeRateRequestDataProvider(): array
    {
        return [
            ['currencies', ''],
            ['currencies', []],
            ['currencies', ['AUD']],
        ];
    }

    public function testItFetchesALiveExchangeRateAndStoresThenForTheGivenCurrencies()
    {
        $apiResponse = File::get(base_path('tests/stubs/apilayer-live-response.json'));

        Http::fake([
            'live?*' => Http::response(
                $apiResponse,
                Response::HTTP_OK
            ),
        ]);

        $currencies = Currency::factory()
            ->count(3)
            ->state(new Sequence(
                ['code' => 'aud'],
                ['code' => 'eur'],
                ['code' => 'jpy'],
            ))
            ->create();

        $this->postJson(route('api.exchange-rates', [
            'currencies' => $currencies->pluck('code')->toArray(),
        ]))
            ->assertSuccessful();

        // TODO check the response
    }

    public function testItThrowsAnExchangeRateApiExceptionIfApiFails()
    {
        $this->withoutExceptionHandling()->expectException(ExchangeRateApiException::class);

        Http::fake([
            'live?*' => Http::response(
                [],
                Response::HTTP_BAD_REQUEST
            ),
        ]);

        $currency = Currency::factory()->create();

        $this->postJson(route('api.exchange-rates', [
            'currencies' => $currency->pluck('code')->toArray(),
        ]));
    }
}
