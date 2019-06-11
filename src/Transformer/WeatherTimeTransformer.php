<?php

namespace App\Transformer;

use DateTime;
use DateTimeZone;
use stdClass;

class WeatherTimeTransformer
{
    /**
     * transform
     *      - from OpenWeatherMapAPIClient weather-for-zip response
     *      - to Weather Time response
     * @param string $zip
     * @param stdClass $weatherForZip
     */
    public static function transform(
        string $zip = null,
        stdClass $weatherForZip
    ) {
        return [
            'location_data' => array_filter(
                [
                    'City' => $weatherForZip->name,
                    'Country' => $weatherForZip->sys->country ?? null,
                    'Latitude' => $weatherForZip->coord->lat ?? null,
                    'Longitude' => $weatherForZip->coord->lon ?? null,
                    'Report Time' => self::formatDateTime(
                        $weatherForZip->dt,
                        true
                    ),
                ],
                function ($fieldValue) {
                    return !is_null($fieldValue);
                }
            ),
            'general_weather' => array_filter(
                [
                    'Sunrise' => self::formatDateTime(
                        $weatherForZip->sys->sunrise ?? null
                    ),
                    'Sunset' => self::formatDateTime(
                        $weatherForZip->sys->sunset ?? null
                    ),
                    'Temperature' => !is_null($weatherForZip->main->temp)
                        ? "{$weatherForZip->main->temp} &#8457;"
                        : null,
                    'Minimum Temperature' =>
                        !is_null($weatherForZip->main->temp_min)
                            ? "{$weatherForZip->main->temp_min} &#8457;"
                            : null,
                    'Maximum Temperature' =>
                        !is_null($weatherForZip->main->temp_max)
                            ? "{$weatherForZip->main->temp_max} &#8457;"
                            : null,
                    'Pressure' => $weatherForZip->main->pressure ?? null,
                    'Humidity' => $weatherForZip->main->humidity ?? null,
                    'Visibility' => $weatherForZip->visibility,
                    'Wind Speed' => $weatherForZip->wind->speed ?? null,
                    'Wind Degrees' => $weatherForZip->wind->deg ?? null,
                    'Rain for Last Hour' =>
                        $weatherForZip->rain->{'1h'} ?? null,
                    'Rain for Last 3 Hours' =>
                        $weatherForZip->rain->{'3h'} ?? null,
                    'Cloud Cover' => !is_null($weatherForZip->clouds->all)
                        ? $weatherForZip->clouds->all . '%'
                        : null,
                    'Snow for Last Hour' =>
                        $weatherForZip->snow->{'1h'} ?? null,
                    'Snow for Last 3 Hours' =>
                        $weatherForZip->snow->{'3h'} ?? null,
                ],
                function ($fieldValue) {
                    return !is_null($fieldValue);
                }
            ),
            'weather_reports' => $weatherForZip->weather,
            'zip' => $zip,
        ];
    }

    /**
     * format given datetime for WeatherTime page
     *
     * @param int|null $datetime
     * @param int|null $timezone
     * @param bool $includeSeconds
     */
    private static function formatDateTime(
        int $datetime = null,
        bool $includeSeconds = false
    ) {
        if (is_null($datetime)) {
            return;
        }
        return (new DateTime("@{$datetime}"))
            ->format(
                'n/j/Y H:i' . (!empty($includeSeconds) ? ':s' : '') . ' T'
            );
    }
}
