<?php

namespace App\APIClient;

use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpClient\HttpClient;

/**
 * OpenWeatherMap API utility to get current weather for a given zip
 */
class WeatherForZip
{
    /** @var ContainerInterface */
    private $container;
    /** @var Array */
    private $errors;
    /** @var Array */
    private $generalWeather;
    /** @var Array */
    private $locationData;
    /** @var Array */
    private $weatherReports;
    /** @var String */
    private $zip;

    /**
     * class constructor
     * @param String|null $zip
     */
    public function __construct(
        String $zip = null,
        ParameterBagInterface $params
    ) {
        if (isset($zip)) {
            $this->setZip($zip);
            $this->run($params);
        }
    }

    /* ========================== */
    /* ===== Business Logic ===== */
    /* ========================== */

    /**
     * run API call to get current weather
     * @param ParameterBagInterface $params
     */
    private function run(ParameterBagInterface $params)
    {
        // setup HttpClient for API request
        $httpClient = HttpClient::create();
        $apiResponse = $httpClient->request(
            'GET',
            $params->get('open_weather_map')['api_url'],
            [
                'query' => [
                    'appid' => $params->get('open_weather_map')['api_key'],
                    'units' => 'imperial',
                    'zip' => $this->getZip() . ',us',
                ]
            ]
        );
        if ($apiResponse->getStatusCode() === 200) {
            try {
                $this->parseResult(json_decode($apiResponse->getContent()));
            } catch (Exception $e) {
                $this->addError(
                    'There was a problem parsing the results: ' .
                    $e->getMessage()
                );
            }
        } else {
            $errorResponse = json_decode($apiResponse->getContent(false));
            $this->addError($errorResponse->message ?? 'Unkown Error');
        }
    }

    /**
     * validate class is ready to make API call
     */
    private function validate()
    {
        $isValid = true;
        if (!preg_match('/^[0-9]{5}(-[0-9]{4})?$/', $this->getZip())) {
            $this->addError('Invalid Zip Code');
            $isValid = false;
        }
        return $isValid;
    }

    /**
     * parse API call result into usable class fields
     * @var \stdClass $result
     */
    private function parseResult(\stdClass $result)
    {
        // set location data
        $this->setLocationData(
            array_filter(
                [
                    'City' => $result->name,
                    'Country' => $result->sys->country ?? null,
                    'Latitude' => $result->coord->lat ?? null,
                    'Longitude' => $result->coord->lon ?? null,
                    'Report Time' => $this->formatDateTime($result->dt),
                ],
                function ($fieldValue) {
                    return !is_null($fieldValue);
                }
            )
        );

        // set general weather data
        $this->setGeneralWeather(
            array_filter(
                [
                    'Sunrise' => $this->formatDateTime($result->sys->sunrise ?? null),
                    'Sunset' => $this->formatDateTime($result->sys->sunset ?? null),
                    'Temperature' => !is_null($result->main->temp)
                        ? "{$result->main->temp} &#8457;"
                        : null,
                    'Minimum Temperature' => !is_null($result->main->temp_min)
                        ? "{$result->main->temp_min} &#8457;"
                        : null,
                    'Maximum Temperature' => !is_null($result->main->temp_max)
                        ? "{$result->main->temp_max} &#8457;"
                        : null,
                    'Pressure' => $result->main->pressure ?? null,
                    'Humidity' => $result->main->humidity ?? null,
                    'Visibility' => $result->visibility,
                    'Wind Speed' => $result->wind->speed ?? null,
                    'Wind Degrees' => $result->wind->deg ?? null,
                    'Rain for Last Hour' => $result->rain->{'1h'} ?? null,
                    'Rain for Last 3 Hours' => $result->rain->{'3h'} ?? null,
                    'Cloud Cover' => !is_null($result->clouds->all)
                        ? $result->clouds->all . '%'
                        : null,
                    'Snow for Last Hour' => $result->snow->{'1h'} ?? null,
                    'Snow for Last 3 Hours' => $result->snow->{'3h'} ?? null,
                ],
                function ($fieldValue) {
                    return !is_null($fieldValue);
                }
            )
        );
        // set weatherReport
        $this->setWeatherReports($result->weather);
    }

    /**
     * format a datetime
     * @param int|null $datetime
     * @param String format
     */
    private function formatDateTime(
        int $datetime = null,
        String $format = 'n/j/Y H:i:s T'
    ) {
        return !is_null($datetime) ? date($format, $datetime) : null;
    }

    /* ===================== */
    /* ===== Accessors ===== */
    /* ===================== */
    /**
     * accessor method for $errors class field
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * accessor method for $generalWeather
     */
    public function getGeneralWeather()
    {
        return $this->generalWeather;
    }

    /**
     * accessor method for $locationData
     */
    public function getLocationData()
    {
        return $this->locationData;
    }

    /**
     * accessor method for $weatherReports
     */
    public function getWeatherReports()
    {
        return $this->weatherReports;
    }

    /**
     * accessor method for $zip class field
     * @return String|null
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * accessor method to identify if class field $errors is filled
     * @return boolean true if errors exists, false otherwise
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }

    /**
     * accessor method to identify if class field $generalWeather is filled
     * @return boolean true if general weather data esists, false otherwise
     */
    public function hasGeneralWeather()
    {
        return !empty($this->generalWeather);
    }

    /**
     * accessor method to identify if class field $locationData is filled
     * @return boolean true if location data esists, false otherwise
     */
    public function hasLocationData()
    {
        return !empty($this->locationData);
    }

    /**
     * accessor method to identify if class field $weatherReports is filled
     * @return boolean true if weather reports exist, false otherwise
     */
    public function hasWeatherReports()
    {
        return !empty($this->weatherReports);
    }

    /* ==================== */
    /* ===== Mutators ===== */
    /* ==================== */

    /**
     * mutator method to add member to $errors class field
     * @var Array $errors
     */
    private function addError($error)
    {
        $this->errors[] = $error;
    }

    /**
     * mutator method to set $generalWeather class field
     * @var Array $generalWeather
     */
    private function setGeneralWeather(array $generalWeather)
    {
        $this->generalWeather = $generalWeather;
    }

    /**
     * mutator method to set $locationData class field
     * @var Array $locationData
     */
    private function setLocationData(array $locationData)
    {
        $this->locationData = $locationData;
    }

    /**
     * mutator method to set $weatherReports class field
     * @var Array|null $weatherReports
     */
    private function setWeatherReports(array $weatherReports = null)
    {
        $this->weatherReports = $weatherReports;
    }

    /**
     * mutator method for $zip class field
     * @var String $zip
     */
    private function setZip(String $zip)
    {
        $this->zip = $zip;
    }
}
