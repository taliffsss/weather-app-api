<?php

namespace App\Http\Controllers;

use App\Services\Api\Contracts\MapBoxInterface;
use App\Services\Api\Contracts\OpenWeatherInterface;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * WeatherController handles weather-related operations.
 */
class WeatherController extends Controller
{
    /**
     * Initialize a new instance of the controller.
     *
     * @param OpenWeatherInterface $openWeather A service to communicate with OpenWeather API.
     * @param MapBoxInterface $mapbox A service to communicate with MapBox API.
     */
    public function __construct(private OpenWeatherInterface $openWeather, private MapBoxInterface $mapbox) {}

    /**
     * Get weather forecast for a specified location.
     *
     * @param Request $request Contains query parameters 'lat' and 'lng' for latitude and longitude.
     * @return \Illuminate\Http\Response
     */
    public function getForecast(Request $request)
    {
        try {
            // Validate the incoming request parameters.
            $validatedData = $request->validate([
                'lat' => 'required|numeric|between:-90,90',
                'lng' => 'required|numeric|between:-180,180',
            ]);

            // Fetch the forecast data using OpenWeather API service.
            $data = $this->openWeather->fetch('forecast', [
                'lat' => $request->input('lat'),
                'lon' => $request->input('lng'),
            ]);

            $currentLocalTime = Carbon::now();
            $endOfTomorrow = $currentLocalTime->copy()->addDays(1)->endOfDay();

            // Filter data to get forecast entries only for today and tomorrow.
            $filteredData = collect($data['list'])->filter(function ($entry) use ($currentLocalTime, $endOfTomorrow) {
                $entryDate = Carbon::createFromFormat('Y-m-d H:i:s', $entry['dt_txt']);
                return $entryDate->isBetween($currentLocalTime, $endOfTomorrow); 
            });

            // Find the forecast entry that's closest in time to the current moment.
            $current = $filteredData->sortBy(function ($item) use ($currentLocalTime) {
                $itemTime = Carbon::createFromFormat('Y-m-d H:i:s', $item['dt_txt']);
                return abs($currentLocalTime->diffInSeconds($itemTime));
            })->first();

            // Remove the closest time record from the filtered dataset.
            $updatedData = $filteredData->reject(function ($item) use ($current) {
                return $item['dt'] == $current['dt'];
            })->values();

            // Return the result in a success response.
            return $this->success("Fetch successfully", ['current' => $current, 'original' => $updatedData], Response::HTTP_OK);

        } catch (ValidationException $e) {
            // Handle validation errors and return an error response.
            return $this->error($e->errors(), 422);
        }
    }

    /**
     * Get the current weather for a specified location.
     *
     * @param Request $request Contains query parameters 'lat', 'lng', and optionally 'units' for temperature format.
     * @return \Illuminate\Http\Response
     */
    public function getWeather(Request $request)
    {
        try {
            // Validate the incoming request parameters.
            $validatedData = $request->validate([
                'lat' => 'required|numeric|between:-90,90',
                'lng' => 'required|numeric|between:-180,180',
                'units' => 'nullable',
            ]);

            // Fetch the weather data using OpenWeather API service.
            $data = $this->openWeather->fetch('weather', [
                'lat' => $request->input('lat'),
                'lon' => $request->input('lng'),
                'units' => $request->input('units', 'imperial'),
            ]);

            // Return the result in a success response.
            return $this->success("Fetch successfully", $data, Response::HTTP_OK);

        } catch (ValidationException $e) {
            // Handle validation errors and return an error response.
            return $this->error($e->errors(), 422);
        }
    }

    /**
     * Get city information using a search query.
     *
     * @param Request $request Contains a query parameter 'query' for the city name.
     * @return \Illuminate\Http\Response
     */
    public function getCity(Request $request)
    {
        try {
            // Validate the incoming request parameters.
            $validatedData = $request->validate([
                'query' => 'required|string',
                'types' => 'nullable|string'
            ]);

            // Get the city data using MapBox API service.
            $city = $request->input('query');
            $data = $this->mapbox->fetch("mapbox.places/{$city}.json", [
                'types' => $request->input('types', 'place'),
            ]);

            // Return the result in a success response.
            return $this->success("Fetch successfully", $data, Response::HTTP_OK);

        } catch (ValidationException $e) {
            // Handle validation errors and return an error response.
            return $this->error($e->errors(), 422);
        }
    }
}
