<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Currency extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function code(): Attribute
    {
        return Attribute::set(fn ($value) => Str::of($value)->upper()->toString());
    }

    public function exchangeRates(): HasMany
    {
        return $this->hasMany(ExchangeRate::class, 'currency', 'code');
    }
}
