<?php

namespace HelloWorld\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class WeatherController extends AbstractActionController
{
	public function indexAction()
	{
		$weather = array(
            // Associative array (old syntax, but still supported in PHP 8)
			'today' => 'Sunny, 18 °C',
			'tomorrow' => 'Cloudy, 12 °C',
            'day after tomorrow' => 'Rainy, 11 °C',
        );

		return new ViewModel([
            // Also an associative array, but with the 'short array syntax' introduced in PHP 5.4
            'content' => $weather
        ]);
	}

	public function filterAction()
	{
		$weather = [
			'today'             => 'Sunny, 18 °C',
			'tomorrow'          => 'Cloudy, 12 °C',
			'day after tomorrow' => 'Rainy, 11 °C',
		];

		// Read the desired day from the query string, e.g. ?day=today
		$day = $this->params()->fromQuery('day', null);

		if ($day !== null && array_key_exists($day, $weather)) {
			// Return only the matching day
			$filtered = [$day => $weather[$day]];
		} else {
			// No valid day supplied: return the full forecast
			$filtered = $weather;
		}

		return new ViewModel([
			'day'     => $day,
			'content' => $filtered,
		]);
	}
}