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
    private $generalWeatherData;
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
                    'city' => $result->name,
                    'country' => $result->sys->country ?? null,
                    'latitude' => $result->coord->lat ?? null,
                    'longitude' => $result->coord->lon ?? null,
                    'report time' => $this->formatDateTime($result->dt),
                ],
                function ($fieldValue) {
                    return !is_null($fieldValue);
                }
            )
        );

        // set general weather data
        $this->setGeneralWeatherData(
            array_filter(
                [
                    'sunrise' => $this->formatDateTime($result->sys->sunrise ?? null),
                    'sunset' => $this->formatDateTime($result->sys->sunset ?? null),
                    'temperature' => !is_null($result->main->temp)
                        ? "{$result->main->temp} &#8457;"
                        : null,
                    'minimum temperature' => !is_null($result->main->temp_min)
                        ? "{$result->main->temp_min} &#8457;"
                        : null,
                    'maximum temperature' => !is_null($result->main->temp_max)
                        ? "{$result->main->temp_max} &#8457;"
                        : null,
                    'pressure' => $result->main->pressure ?? null,
                    'humidity' => $result->main->humidity ?? null,
                    'visibility' => $result->visibility,
                    'wind speed' => $result->wind->speed ?? null,
                    'wind degrees' => $result->wind->deg ?? null,
                    'rain for last hour' => $result->rain->{'1h'} ?? null,
                    'rain for last 3 hours' => $result->rain->{'3h'} ?? null,
                    'cloud cover' => !is_null($result->clouds->all)
                        ? $result->clouds->all . '%'
                        : null,
                    'snow for last hour' => $result->snow->{'1h'} ?? null,
                    'snow for last 3 hours' => $result->snow->{'3h'} ?? null,
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
    private function formatDateTime(int $datetime = null, String $format = 'n/j/Y H:i:s T')
    {
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
     * accessor method for $generalWeatherData
     */
    public function getGeneralWeatherData()
    {
        return $this->generalWeatherData;
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
     * accessor method to identify if class field $generalWeatherData is filled
     * @return boolean true if general weather data esists, false otherwise
     */
    public function hasGeneralWeatherData()
    {
        return !empty($this->generalWeatherData);
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
     * mutator method to set $generalWeatherData class field
     * @var Array $generalWeatherData
     */
    private function setGeneralWeatherData(array $generalWeatherData)
    {
        $this->generalWeatherData = $generalWeatherData;
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
