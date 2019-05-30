<?php

namespace App\Controller;

use App\APIClient\WeatherForZip;
use App\Form\WeatherTimeForm;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * WeatherTimeController
 */
class WeatherTimeController extends AbstractController
{
    /**
     * @Route("", name="weather-time")
     */
    public function __invoke(Request $request, ParameterBagInterface $params)
    {
        $weatherTimeForm = new WeatherTimeForm();
        $weatherTimeForm->setZip(
            $request->request->get('form')['zip']
                ?? $request->getSession()->get('zip')
        );
        $form = $this->createForm(WeatherTimeForm::class, $weatherTimeForm);
        $form->handleRequest($request);

        $viewParams = [
            'errors' => null,
            'form' => $form->createView(),
            'generalWeatherData' => null,
            'locationData' => null,
            'weatherReports' => null,
            'zip' => null
        ];

        if (!$form->isSubmitted() || $form->isValid()) {
            $viewParams['zip'] = $weatherTimeForm->getZip();
            $openWeatherMap = new WeatherForZip(
                $weatherTimeForm->getZip(),
                $params
            );
            if (!$openWeatherMap->hasErrors()) {
                $request->getSession()->set('zip', $form->getData()->getZip());
                $viewParams = array_merge(
                    $viewParams,
                    [
                        'generalWeatherData' =>
                            $openWeatherMap->hasGeneralWeatherData()
                                ? $openWeatherMap->getGeneralWeatherData()
                                : null,
                        'locationData' => $openWeatherMap->hasLocationData()
                            ? $openWeatherMap->getLocationData()
                            : null,
                        'weatherReports' => $openWeatherMap->hasWeatherReports()
                            ? $openWeatherMap->getWeatherReports()
                            : null,
                    ]
                );
            } else {
                $viewParams['errors'] = $openWeatherMap->getErrors();
            }
        }

        return $this->render('weather-time.html.twig', $viewParams);
    }
}
