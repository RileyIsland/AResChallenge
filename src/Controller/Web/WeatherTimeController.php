<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
