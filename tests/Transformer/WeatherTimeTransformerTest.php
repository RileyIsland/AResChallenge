<?php

namespace App\Tests\Transformer;

use App\Transformer\WeatherTimeTransformer;
use PHPUnit\Framework\TestCase;
use stdClass;

class WeatherTimeTransformerTest extends TestCase
{
    /**
     * @dataProvider validWeatherForZipProvider
     */
    public function testValidWeatherForZip($validZip, $validWeatherForZip)
    {
        $transformed = WeatherTimeTransformer::transform(
            $validZip,
            // TODO: find a better way to cast to stdClass
            json_decode(json_encode($validWeatherForZip))
        );
        $this->assertArrayHasKey('location_data', $transformed);
        $this->assertArrayHasKey('general_weather', $transformed);
        $this->assertArrayHasKey('weather_reports', $transformed);
        $this->assertEquals($validZip, $transformed['zip']);
    }

    /*
     * data provider for testValidWeatherForZip
     */
    public function validWeatherForZipProvider() {
        return [
            [
                "92109",
                [
                    "coord" => [
                        "lon" => "-117.21",
                        "lat" => "32.78",
                    ],
                    "weather" => [
                        "0" => [
                            "id" => "804",
                            "main" => "Clouds",
                            "description" => "overcast clouds",
                            "icon" => "04d",
                        ],
                    ],
                    "base" => "stations",
                    "main" => [
                        "temp" => "69.3",
                        "pressure" => "1014",
                        "humidity" => "68",
                        "temp_min" => "62.01",
                        "temp_max" => "75.99",
                    ],
                    "visibility" => "14484",
                    "wind" => [
                        "speed" => "8.05",
                        "deg" => "260",
                    ],
                    "clouds" => [
                        "all" => "90",
                    ],
                    "dt" => "1559937065",
                    "sys" => [
                        "type" => "1",
                        "id" => "5026",
                        "message" => "0.0096",
                        "country" => "US",
                        "sunrise" => "1559911241",
                        "sunset" => "1559962499",
                    ],
                    "timezone" => "-25200",
                    "id" => "420008330",
                    "name" => "San Diego",
                    "cod" => "200",
                ]
            ]
        ];
    }
}
