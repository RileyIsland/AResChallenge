<?php

namespace App\Controller;

use App\APIClient\OpenWeatherMap;
use App\Transformer\WeatherTime;
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
     * @Route("", methods="GET", name="weather-time.index")
     */
    public function index(Request $request)
    {
        return $this->render(
            'weather-time.html.twig',
            [
                'zip' => $request->getSession()->get('zip')
            ]
        );
    }

    /**
     * @Route("", methods="POST", name="weather-time.show")
     */
    public function show(OpenWeatherMap $apiClient, Request $request)
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

        return new JsonResponse(WeatherTime::transform($zip, $weatherForZip));
    }
}
