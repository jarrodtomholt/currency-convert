<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CurrencyResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            $this->mergeWhen($this->exchangeRates->isNotEmpty(), [
                'exchangeRates' => ExchangeRateResource::collection($this->exchangeRates),
            ]),
        ];
    }
}
