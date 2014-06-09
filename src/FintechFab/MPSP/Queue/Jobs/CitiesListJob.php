<?php namespace FintechFab\MPSP\Queue\Jobs;

use FintechFab\MPSP\Services\TransferService;

class CitiesListJob extends AbstractJob
{

	public function __construct(TransferService $transfer)
	{
		$this->transfer = $transfer;
		$this->apiQueue = $this->getAPIQueueInterface();
	}

	public function run(array $requestData)
	{
		$cities = $this->transfer->cities();

		$this->apiQueue->push('citiesListResult', [
			'cities' => $cities,
		]);
	}
}