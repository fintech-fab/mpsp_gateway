<?php namespace FintechFab\MPSP\Queue\Jobs;

use FintechFab\MPSP\Services\TransferService;

class CalculateFeeJob extends AbstractJob
{

	/**
	 * @var \FintechFab\MPSP\Services\TransferService
	 */
	protected $transfer;
	/**
	 * @var \Illuminate\Queue\QueueInterface
	 */
	protected $apiQueue;

	public function __construct(TransferService $transfer)
	{
		$this->transfer = $transfer;
		$this->apiQueue = $this->getAPIQueueInterface();
	}

	public function run(array $requestData)
	{
		$commission = $this->transfer->fee($requestData['city_id'], $requestData['amount']);

		// ставим задачу в API с результатом
		$this->apiQueue->push('calculateFeeResult', [
			'cost_id'    => $requestData['cost_id'],
			'city_id'    => $requestData['city_id'],
			'amount'     => $requestData['amount'],
			'currency'   => $requestData['currency'],
			'commission' => $commission,
		]);
	}

}