<?php

// TODO: figure out proper way to do these tests and remove this class,
// TODO: because test should not call an external API

namespace App\Tests\Controller\API;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class WeatherTimeControllerTest extends WebTestCase
{
    public function testShow()
    {
        $client = static::createClient();

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
        $invalidZip = 'abcde';
        $client->xmlHttpRequest(
            'POST',
            '/',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'zip' => $invalidZip
            ])
        );
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
        $responseContent = json_decode($response->getContent());
        $this->assertEquals(
            'Validation Error: Invalid Zip',
            $responseContent->errors[0]
        );
        $this->assertEquals($invalidZip, $responseContent->zip);

        // test city not found
        $nonExistentZip = '00000';
        $client->xmlHttpRequest(
            'POST',
            '/',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'zip' => $nonExistentZip
            ])
        );
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
        $responseContent = json_decode($response->getContent());
        $this->assertEquals(
            'Error retrieving results from API: city not found',
            $responseContent->errors[0]
        );
        $this->assertEquals($nonExistentZip, $responseContent->zip);
    }
}
