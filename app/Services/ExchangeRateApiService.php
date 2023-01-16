<?php

namespace App\Services;

use App\Exceptions\ExchangeRateApiException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ExchangeRateApiService
{
    public PendingRequest $http;

    public string $sourceCurrency = 'USD';

    public function __construct(string $apiKey)
    {
        $this->http = Http::baseUrl('https://api.apilayer.com/currency_data')
            ->accept('application/json')
            ->withHeaders(['apiKey' => $apiKey]);
    }

    public function live(Collection $currencies): Collection
    {
        try {
            $response = $this->http->get('live', [
                'currencies' => $currencies->implode(','),
                'source' => $this->sourceCurrency,
            ])->throw();

            return Collection::make($response->json('quotes'))->map(function (float $rate, string $currencyCode) {
                $currencyCode = Str::of($currencyCode)
                    ->replaceFirst($this->sourceCurrency, '')
                    ->toString();

                return (object) [
                    'currency' => $currencyCode,
                    'rate' => $rate,
                ];
            })->values();
        } catch (RequestException $throwable) {
            report($throwable);
            throw new ExchangeRateApiException;
        }
    }

    public function list(): Collection
    {
        $response = $this->http->get('list')->throw();

        return Collection::make($response->json('currencies'));
    }

    public function timeframe(Collection $currencies, Carbon $from, Carbon $to): Collection
    {
        try {
            $response = $this->http->get('timeframe', [
                'currencies' => $currencies->implode(','),
                'start_date' => $from->format('Y-m-d'),
                'end_date' => $to->addDay()->format('Y-m-d'),
                'source' => $this->sourceCurrency,
            ])->throw();

            return Collection::make($response->json('quotes'))->map(function (array $rates, string $date) {
                return collect($rates)->map(function (float $rate, string $currencyCode) use ($date) {
                    $currencyCode = Str::of($currencyCode)
                        ->replaceFirst($this->sourceCurrency, '')
                        ->toString();

                    return (object) [
                        'currency' => $currencyCode,
                        'date' => Carbon::parse($date),
                        'rate' => $rate,
                    ];
                })->values();
            })->flatten();
        } catch (RequestException $throwable) {
            report($throwable);
            throw new ExchangeRateApiException;
        }
    }
}
