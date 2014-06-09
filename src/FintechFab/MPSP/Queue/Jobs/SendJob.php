<?php namespace FintechFab\MPSP\Queue\Jobs;

use FintechFab\MPSP\Exceptions\TransferException;
use FintechFab\MPSP\Services\TransferService;
use Log;

class SendJob extends AbstractJob
{

	/**
	 * @var \FintechFab\MPSP\Services\TransferService
	 */
	private $transfer;

	public function __construct(TransferService $transfer)
	{
		$this->transfer = $transfer;
	}

	public function run(array $requestData)
	{
		$transferId = $requestData['transfer']['id'];

		try {
			Log::info('SendJob::run() send a request to transfer', $requestData);
			$checkNumber = $this->transfer->pay($transferId, $requestData);
		} catch (TransferException $exception) {

			Log::warning('SendJob::run()', [
				'transfer_id' => $transferId,
				'error'       => $exception->getMessage(),
				'trace'       => $exception->getTraceAsString()
			]);

			$this->getAPIQueueInterface()->push('transferSendResult', [
				'transfer_id' => $transferId,
				'error'       => [
					'code'    => $exception->getCode(),
					'message' => $exception->getMessage(),
				],
			]);

			// Не перевыставлять задачу
			return;
		}

		Log::info('SendJob::run() Finished successfully', $requestData);

		// ставим задачу в API с результатом
		$this->getAPIQueueInterface()->push('transferSendResult', [
			'checknumber' => $checkNumber,
			'transfer_id' => $transferId,
		]);

		Log::info('SendJob::run() Send job: transferSendResult.', ['checknumber' => $checkNumber]);

	}
}