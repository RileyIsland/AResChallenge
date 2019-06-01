<?php

namespace App\Controller;

use App\APIClient\WeatherForZip;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * WeatherTimeController
 */
class WeatherTimeController extends AbstractController
{
    /**
     * @Route("weather-time", methods="GET", name="weather-time.index")
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
     * @Route("weather-time", methods="POST", name="weather-time.show")
     */
    public function show(Request $request, ParameterBagInterface $params)
    {
        $zip = json_decode($request->getContent())->zip ?? null;

        $responseData = [
             'errors' => null,
             'general_weather' => null,
             'location_data' => null,
             'weather_reports' => null,
             'zip' => $zip
         ];

        if (preg_match('/^[0-9]{5}(-[0-9]{4})?$/', $zip)) {
            $openWeatherMap = new WeatherForZip($zip, $params);
            if (!$openWeatherMap->hasErrors()) {
                $request->getSession()->set('zip', $zip);
                $responseData = array_merge(
                     $responseData,
                     [
                         'general_weather' =>
                             $openWeatherMap->hasGeneralWeather()
                                 ? $openWeatherMap->getGeneralWeather()
                                 : null,
                         'location_data' => $openWeatherMap->hasLocationData()
                             ? $openWeatherMap->getLocationData()
                             : null,
                         'weather_reports' => $openWeatherMap->hasWeatherReports()
                             ? $openWeatherMap->getWeatherReports()
                             : null,
                     ]
                 );
            } else {
                $responseData['errors'] = $openWeatherMap->getErrors();
            }
        } else {
            $responseData['errors'][] = 'Invalid Zip';
        }

        return new JsonResponse($responseData);
    }
}
