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
        $this->assertEquals('92109', $responseContent->zip);
    }
}
