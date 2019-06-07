<?php

namespace App\APIClient;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;

abstract class AbstractAPIClient implements HttpClientInterface
{
    protected $httpClient;

    /**
     * @param array $defaultOptions     Default requests' options
     * @param int   $maxHostConnections The maximum number of connections to a single host
     * @param int   $maxPendingPushes   The maximum number of pushed responses to accept in the queue
     */
    public function __construct(
        array $defaultOptions = [],
        int $maxHostConnections = 6,
        int $maxPendingPushes = 50
    ) {
        $this->httpClient = HttpClient::create(
            $defaultOptions,
            $maxHostConnections,
            $maxPendingPushes
        );
    }

    /**
     * @see HttpClientInterface::OPTIONS_DEFAULTS for available options
     *
     * {@inheritdoc}
     */
    public function request(
        string $method = 'GET',
        string $url = '',
        array $options = []
    ): ResponseInterface {
        return $this->httpClient->request($method, $url, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function stream(
        $responses,
        float $timeout = null
    ): ResponseStreamInterface {
        return $this->httpClient->stream($responses, $timeout);
    }
}
