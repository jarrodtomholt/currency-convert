<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExchangeRateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'currencies' => ['required', 'array', 'exists:App\Models\Currency,code'],
        ];
    }

    public function passedValidation(): void
    {
        $this->replace([
            'currencies' => collect($this->currencies),
        ]);
    }
}
