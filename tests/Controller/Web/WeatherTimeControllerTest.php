<?php

namespace App\Tests\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class WeatherTimeControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $client->request('GET', '');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
