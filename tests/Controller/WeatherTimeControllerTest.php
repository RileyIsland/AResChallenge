<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class WeatherTimeControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testShow()
    {
        $client = static::createClient();

        // test valid zip
        $client->xmlHttpRequest(
            'POST',
            '/',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'zip' => '92109'
            ])
        );
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $responseContent = json_decode($response->getContent());
        $this->assertObjectHasAttribute('location_data', $responseContent);
        $this->assertObjectHasAttribute('general_weather', $responseContent);
        $this->assertObjectHasAttribute('weather_reports', $responseContent);
        $this->assertEquals('92109', $responseContent->zip);

        // test missing zip
        $client->xmlHttpRequest(
            'POST',
            '/',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ]
        );
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
        $responseContent = json_decode($response->getContent());
        $this->assertEquals(
            'Validation Error: Invalid Zip',
            $responseContent->errors[0]
        );
        $this->assertNull($responseContent->zip);

        // test invalid zip
        $client->xmlHttpRequest(
            'POST',
            '/',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'zip' => 'abcde'
            ])
        );
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
        $responseContent = json_decode($response->getContent());
        $this->assertEquals(
            'Validation Error: Invalid Zip',
            $responseContent->errors[0]
        );
        $this->assertEquals('abcde', $responseContent->zip);

        // test city not found
        $client->xmlHttpRequest(
            'POST',
            '/',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'zip' => '00000'
            ])
        );
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
        $responseContent = json_decode($response->getContent());
        $this->assertEquals(
            'Error retrieving results from API: city not found',
            $responseContent->errors[0]
        );
        $this->assertEquals('00000', $responseContent->zip);

    }
}
