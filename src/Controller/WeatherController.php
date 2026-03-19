<?php

namespace HelloWorld\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class WeatherController extends AbstractActionController
{
	private array $weather = [
		'Today'              => 'Sunny, 18 °C',
		'Tomorrow'           => 'Cloudy, 12 °C',
		'Day after tomorrow' => 'Rainy, 11 °C',
	];

	public function getWeather(): array
	{
		return $this->weather;
	}

	public function setWeather(array $weather): void
	{
		$this->weather = $weather;
	}

	public function indexAction()
	{
		return new ViewModel([
            // use getter method defined in this class
			'content' => $this->getWeather(),
		]);
	}

	public function filterAction()
	{
		// Read the desired day from the query string, e.g. ?day=today
		$day = $this->params()->fromQuery('day', null);

        // Use the getter method to get the weather data
		$weather = $this->getWeather();

        // Filter the weather data based on the day from query string
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