<?php namespace FintechFab\MPSP\Queue\Jobs;

use FintechFab\MPSP\Entities\Card;
use FintechFab\MPSP\Exceptions\AcquiringException;
use FintechFab\MPSP\Services\AcquiringService;
use Log;

class AcquiringRefundJob extends AbstractJob
{

	public function __construct(AcquiringService $acquring, Card $oCard)
	{
		$this->acquring = $acquring;
		$this->card = $oCard;
		$this->apiQueue = $this->getAPIQueueInterface();
	}

	public function run(array $requestData)
	{
		$transferId = $requestData['transfer']['id'];

		Log::info('Do refund', $requestData);

		try {
			$this->acquring->doRefund($transferId, $requestData);

			// сообщаем в API о результате
			$this->apiQueue->push('acquiringRefundResult', [
				'transfer_id' => $transferId,
			]);

		} catch (AcquiringException $exception) {

			Log::error($exception);

			// сообщаем в API о результате
			$this->apiQueue->push('acquiringRefundResult', [
				'transfer_id' => $transferId,
				'error'       => [
					'code'    => $exception->getCode(),
					'message' => $exception->getMessage(),
				],
			]);
		}
	}

}