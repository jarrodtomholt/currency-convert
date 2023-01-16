<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Currency;
use App\Models\ExchangeRate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Cache::forget('currencies');

        $currencies = File::get(base_path('tests/stubs/apilayer-list-response.json'));

        collect(json_decode($currencies, true)['currencies'])->each(function (string $name, string $code) {
            Currency::create([
                'code' => $code,
                'name' => $name,
            ]);
        });

        $history = File::get(base_path('tests/stubs/apilayer-timeframe-response.json'));

        collect(json_decode($history, true)['quotes'])->each(function (array $rates, string $date) {
            collect($rates)->map(function (float $rate, string $currencyCode) use ($date) {
                $currencyCode = Str::of($currencyCode)
                    ->replaceFirst('USD', '')
                    ->toString();

                ExchangeRate::create([
                    'currency' => $currencyCode,
                    'date' => $date,
                    'rate' => $rate,
                ]);
            });
        });
    }
}
