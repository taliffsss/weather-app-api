<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Api\Contracts\MapBoxInterface;
use App\Services\Api\Contracts\OpenWeatherInterface;
use App\Services\Api\MapBoxService;
use App\Services\Api\OpenWeatherService;
use GuzzleHttp\Client;

class ApiServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(MapBoxInterface::class, function($app) {
            return new MapBoxService(new Client([
                'base_uri' => env('MAPBOX_API'),
            ]), env('MAPBOX_KEY'));
        });

        $this->app->bind(OpenWeatherInterface::class, function($app) {
            return new OpenWeatherService(new Client([
                'base_uri' => env('OPEN_WEATHER_API'),
            ]), env('OPEN_WEATHER_KEY'));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
