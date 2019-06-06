<?php

namespace App\APIClient;

use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * OpenWeatherMap API client
 */
class OpenWeatherMap extends AbstractAPIClient
{
    /** @var string */
    private $apiKey;

    /** @var string */
    private $apiUrl = 'http://api.openweathermap.org/data/2.5/';

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
        parent::__construct();
    }

    public function getWeatherForZip(string $zip = null)
    {
        $apiResponse = parent::request(
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
