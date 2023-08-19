<?php

namespace App\Http\Controllers;

use App\Services\Api\Contracts\MapBoxInterface;
use App\Services\Api\Contracts\OpenWeatherInterface;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Carbon\Carbon;

class WeatherController extends Controller
{
    public function __construct(private OpenWeatherInterface $openWeather, private MapBoxInterface $mapbox) {}

    public function getForecast(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'lat' => 'required|numeric|between:-90,90',
                'lng' => 'required|numeric|between:-180,180',
            ]);

            $data = $this->openWeather->fetch('forecast', [
                'lat' => $request->input('lat'),
                'lon' => $request->input('lng'),
            ]);


            $currentLocalTime = Carbon::now();

            // Sort data based on the difference between the current time and each entry's time.
            $current = collect($data['list'])->sortBy(function ($item) use ($currentLocalTime) {
                $itemTime = Carbon::createFromFormat('Y-m-d H:i:s', $item['dt_txt']);
                return abs($currentLocalTime->diffInSeconds($itemTime)); // Return the absolute difference in seconds.
            })->first();

            // Remove the closest data from the original data.
            $updatedData = collect($data['list'])->reject(function ($item) use ($current) {
                return $item['dt'] == $current['dt'];
            })->values();

            return $this->success("Fetch successfully", ['current' => $current, 'original' => $updatedData], Response::HTTP_OK);

        } catch (ValidationException $e) {
            return $this->error($e->errors(), 422);
        }
    }

    public function getWeather(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'lat' => 'required|numeric|between:-90,90',
                'lng' => 'required|numeric|between:-180,180',
                'units' => 'nullable',
            ]);

            $data = $this->openWeather->fetch('weather', [
                'lat' => $request->input('lat'),
                'lon' => $request->input('lng'),
                'units' => $request->input('units', 'imperial'),
            ]);

            return $this->success("Fetch successfully", $data, Response::HTTP_OK);

        } catch (ValidationException $e) {
            return $this->error($e->errors(), 422);
        }
    }

    public function getCity(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'query' => 'required|string',
                'types' => 'nullable"string'
            ]);

            $city = $request->input('query');

            $data = $this->mapbox->fetch("mapbox.places/{$city}.json", [
                'types' => $request->input('types', 'place'),
            ]);

            return $this->success("Fetch successfully", $data, Response::HTTP_OK);
        } catch (ValidationException $e) {
            return $this->error($e->errors(), 422);
        }
    }
}
