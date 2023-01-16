<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ExchangeRate extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
    ];

    public function currency(): Attribute
    {
        return Attribute::set(fn ($value) => Str::of($value)->upper()->toString());
    }

    public function date(): Attribute
    {
        return Attribute::set(function ($value) {
            if ($value instanceof DateTime) {
                return $value->format('Y-m-d');
            }

            return Carbon::parse($value)->format('Y-m-d');
        });
    }
}
