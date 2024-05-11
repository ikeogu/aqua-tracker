<?php

namespace App\Providers;

use App\Http\Clients\PaystackClient;
use Illuminate\Support\ServiceProvider;

class PaystackServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
        $this->app->singleton(PaystackClient::class, function () {
            $client = new PaystackClient();

            $client->baseUrl(config('services.paystack.base_url'))
                ->withHeaders([
                    'Cache-Control' => 'no-cache',
                    'Authorization' => 'Bearer ' . config('services.paystack.secret'),
                    'content-type' => 'application/json'
                ])
                ->withOptions([]);

            return $client;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
