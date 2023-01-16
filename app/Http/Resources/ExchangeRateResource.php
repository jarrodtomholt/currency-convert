<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExchangeRateResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'currency' => $this->currency,
            'rate' => $this->rate,
            'date' => $this->date ?? now()->toDateString(),
        ];
    }
}
