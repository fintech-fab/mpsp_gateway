<?php namespace FintechFab\MPSP\Queue\Jobs;

use MPSP;

class CitiesListJob extends AbstractJob
{

	public function __construct()
	{
		$this->apiQueue = $this->getAPIQueueInterface();
	}

	public function run(array $requestData)
	{
		$cities = MPSP::cities();

		$this->apiQueue->push('citiesListResult', [
			'cities' => $cities,
		]);
	}
}