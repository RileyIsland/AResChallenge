<?php

namespace App\Controller\API;

use App\APIClient\OpenWeatherMapAPIClient;
use App\Transformer\WeatherTimeTransformer;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * WeatherTimeController
 */
class WeatherTimeController extends AbstractController
{
    /**
     * @Route("", methods="POST", name="weather-time.show")
     */
    public function show(OpenWeatherMapAPIClient $apiClient, Request $request)
    {
        $zip = json_decode($request->getContent())->zip ?? null;

        if (!preg_match('/^[0-9]{5}$/', $zip)) {
            return new JsonResponse(
                [
                    'errors' => [
                        'Validation Error: Invalid Zip',
                    ],
                    'zip' => $zip,
                ],
                400
            );
        }

        try {
            $weatherForZip = $apiClient->getWeatherForZip($zip);
        } catch (Exception $e) {
            return new JsonResponse(
                [
                    'errors' => [
                        'Error retrieving results from API: ' .
                        $e->getMessage(),
                    ],
                    'zip' => $zip,
                ],
                400
            );
        }

        return new JsonResponse(
            WeatherTimeTransformer::transform($zip, $weatherForZip)
        );
    }
}
