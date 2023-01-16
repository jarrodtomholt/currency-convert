<?php

namespace App\Models;

use App\Enums\ReportInterval;
use App\Enums\ReportStatus;
use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeRateReport extends Model
{
    use HasFactory, HasUser;

    protected $guarded = [];

    protected $casts = [
        'currencies' => AsCollection::class,
        'interval' => ReportInterval::class,
        'from' => 'date',
        'to' => 'date',
        'status' => ReportStatus::class,
    ];

    public function from(): Attribute
    {
        return Attribute::set(fn ($value) => $value->startOfDay());
    }

    public function to(): Attribute
    {
        return Attribute::set(fn ($value) => $value->endOfDay());
    }
}
