<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CurrencyResource;
use App\Models\Currency;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Cache;

class CurrencyController extends Controller
{
    public function __invoke(): JsonResource
    {
        $currencies = Cache::remember('currencies', now()->addHours(24), function () {
            return Currency::all();
        });

        return CurrencyResource::collection($currencies);
    }
}
