<?php

namespace App\Providers;

use App\Services\ExchangeRateApiService;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ExchangeRateApiService::class, function () {
            return new ExchangeRateApiService(config('services.currency-data-api.key'));
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        JsonResource::withoutWrapping();

        Gate::define('view.report', function (User $user, Post $post) {
            return $user->id === $post->user_id;
        });
    }
}
