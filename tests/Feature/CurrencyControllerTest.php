<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CurrencyControllerTest extends TestCase
{
    public function testUnauthenticatedUsersCanNotAccess()
    {
        $this->getJson(route('api.currencies'))->assertUnauthorized();
    }

    public function testItReturnsAListOfCurrenciesFromTheDatabase()
    {
        $currencies = Currency::factory()->count(5)->create();

        Sanctum::actingAs(User::factory()->create());

        $response = $this->getJson(route('api.currencies'))
            ->assertSuccessful()
            ->assertJsonCount(5);

        $currencies->each(fn (Currency $currency) => $response->assertJson(fn (AssertableJson $json) => $json->whereContains('code', $currency->code)
                    ->whereContains('name', $currency->name)
            )
        );
    }

    public function testItCachesTheCurrenciesOnFetchFromTheDatabase()
    {
        $this->assertTrue(Cache::missing('currencies'));

        Sanctum::actingAs(User::factory()->create());

        $this->getJson(route('api.currencies'));

        $this->assertTrue(Cache::has('currencies'));
    }
}
