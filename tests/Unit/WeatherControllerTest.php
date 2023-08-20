<?php

namespace Tests\Unit;

use App\Http\Controllers\WeatherController;
use App\Services\Api\Contracts\MapBoxInterface;
use App\Services\Api\Contracts\OpenWeatherInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class WeatherControllerTest extends TestCase
{
    use RefreshDatabase;

    private $mockWeatherService;
    private $mockMapBoxService;

    public function setUp(): void
    {
        parent::setUp();

        $this->mockWeatherService = \Mockery::mock(OpenWeatherInterface::class);
        $this->mockMapBoxService = \Mockery::mock(MapBoxInterface::class);

        $this->app->instance(OpenWeatherInterface::class, $this->mockWeatherService);
        $this->app->instance(MapBoxInterface::class, $this->mockMapBoxService);
    }

    /** @test */
    public function it_returns_success_when_get_weather_with_valid_data()
    {
        $this->mockWeatherService->shouldReceive('fetch')
            ->once()
            ->andReturn(['weather_data' => 'Mocked weather data']);

        $response = $this->get('/api/v1/weather?lat=14.5833&lng=120.9667');
        $response->assertStatus(200);
    }

    /** @test */
    public function it_returns_error_when_get_weather_with_invalid_data()
    {
        $response = $this->get('/api/v1/weather?lat=95&lng=120.9667');
        $response->assertStatus(422);
    }

    /** @test */
    public function it_returns_success_when_get_forecast_with_valid_data()
    {
        $mockData = json_decode(file_get_contents(base_path('tests/stubs/forecast.json')), true);

        $this->mockWeatherService->shouldReceive('fetch')
            ->once()
            ->andReturn([
                'list' => $mockData
            ]);

        $response = $this->get('/api/v1/forecast?lat=14.5833&lng=120.9667');
        $response->assertStatus(200);
    }

    /** @test */
    public function it_returns_error_when_get_forecast_with_invalid_data()
    {
        $response = $this->get('/api/v1/forecast?lat=95&lng=120.9667');
        $response->assertStatus(422);
    }

    /** @test */
    public function it_returns_success_when_get_city_with_valid_query()
    {
        $this->mockMapBoxService->shouldReceive('fetch')
            ->once()
            ->andReturn(['city_data' => 'Mocked city data']);

        $response = $this->get('/api/v1/city?query=manila');
        $response->assertStatus(200);
    }

    /** @test */
    public function it_returns_error_when_get_city_with_invalid_query()
    {
        $response = $this->get('/api/v1/city?query=');
        $response->assertStatus(422);
    }

    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }
}
