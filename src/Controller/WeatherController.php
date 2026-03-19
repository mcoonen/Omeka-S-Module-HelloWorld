<?php

namespace HelloWorld\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class WeatherController extends AbstractActionController
{
	public function indexAction()
	{
		$weather = array(
			'today' => 'Sunny, 18 °C',
			'tomorrow' => 'Cloudy, 12 °C',
            'day after tomorrow' => 'Rainy, 11 °C',
        );

		return new ViewModel([
            'content' => $weather
        ]);

	}
}