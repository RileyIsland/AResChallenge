<?php

namespace App\APIClient;

use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpClient\HttpClient;

/**
 * OpenWeatherMap API client
 */
class OpenWeatherMap
{
    private $apiKey;
    private $apiUrl = 'http://api.openweathermap.org/data/2.5/';
    private $httpClient;

    public function __construct(ParameterBagInterface $params)
    {
        $this->httpClient = HttpClient::create();
        $this->apiKey = $params->get('open_weather_map')['api_key'];
    }

    public function request(string $endpoint = '', array $query = [])
    {
        return $this->httpClient->request(
            'GET',
            $this->apiUrl . $endpoint,
            [
                'query' => array_merge(
                    $query,
                    [
                        'appid' => $this->apiKey
                    ]
                )
            ]
        );
    }

    public function getWeatherForZip(string $zip = null)
    {
        $apiResponse = $this->request(
            'weather',
            [
                'units' => 'imperial',
                'zip' => $zip . ',us',
            ]
        );
        if ($apiResponse->getStatusCode() === 200) {
            return json_decode($apiResponse->getContent());
        } else {
            $errorResponse = json_decode($apiResponse->getContent(false));
            throw new Exception($errorResponse->message ?? 'Unkown Error');
        }
    }
}
