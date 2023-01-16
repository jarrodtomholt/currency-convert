<?php

namespace Tests\Unit;

use App\Models\Currency;
use Tests\TestCase;

class CurrencyCodeUppercaseTest extends TestCase
{
    public function testItEnforcesUppercaseCodesForAllCurrencies()
    {
        $currency = Currency::factory()->make([
            'code' => 'shouldbeuppercase',
        ]);

        $this->assertSame('SHOULDBEUPPERCASE', $currency->code);
    }
}
