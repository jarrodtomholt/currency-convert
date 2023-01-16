<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExchangeRateReportResponse extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id, // ideally hash/uuid
            'currencies' => $this->currencies,
            'status' => $this->status,
            'interval' => $this->interval,
            'from' => $this->from->format('Y-m-d'),
            'to' => $this->to->format('Y-m-d'),
            $this->mergeWhen($this->additional['rates'] ?? false, fn () => [
                'rates' => $this->additional['rates'],
            ]),
        ];
    }
}
