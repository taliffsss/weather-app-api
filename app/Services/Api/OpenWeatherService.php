<?php
namespace App\Services\Api;

use App\Services\Api\Contracts\OpenWeatherInterface;
use GuzzleHttp\Client;

class OpenWeatherService implements OpenWeatherInterface {

    public function __construct(protected Client $client, protected string $apiKey) {
        $this->client = $client;
        $this->apiKey = $apiKey;
    }

    public function fetch(string $endpoint, ?array $params = []): array
    {
        $options = [
            'query' => array_merge(...array_filter([['appid' => $this->apiKey], $params])),
        ];
        $response = $this->client->request('GET', $endpoint, $options);
        
        return json_decode($response->getBody(), true);
    }
}
