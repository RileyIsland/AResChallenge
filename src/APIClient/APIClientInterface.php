<?php

namespace App\APIClient;

use Symfony\Contracts\HttpClient\ResponseInterface;

interface APIClientInterface
{
    public function request(
        string $method,
        string $url,
        array $options = []
    ): ResponseInterface;
}
