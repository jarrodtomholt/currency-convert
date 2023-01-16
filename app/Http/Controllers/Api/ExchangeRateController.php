<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExchangeRateRequest;
use App\Http\Resources\ExchangeRateResource;
use App\Services\ExchangeRateApiService;

class ExchangeRateController extends Controller
{
    public function __invoke(ExchangeRateRequest $request, ExchangeRateApiService $api)
    {
        $exchangeRates = $api->live($request->currencies);

        return ExchangeRateResource::collection($exchangeRates);
    }
}
