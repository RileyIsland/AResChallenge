<?php

namespace App\APIClient;

use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * OpenWeatherMap API client
 */
class OpenWeatherMap extends AbstractAPIClient
{
    /** @var string */
    private $apiKey;

    /** @var string */
    private $apiUrl = 'http://api.openweathermap.org/data/2.5/';

    public function __construct(HttpClientInterface $httpClient, $apiKey)
    {
        $this->apiKey = $apiKey;
        $this->httpClient = $httpClient;
    }

    public function getWeatherForZip(string $zip = null)
    {
        $apiResponse = $this->request(
            'GET',
            $this->apiUrl . 'weather',
            [
                'query' => [
                    'appid' => $this->apiKey,
                    'units' => 'imperial',
                    'zip' => $zip . ',us',
                ],
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
