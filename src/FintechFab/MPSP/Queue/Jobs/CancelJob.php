<?php namespace FintechFab\MPSP\Queue\Jobs;

use FintechFab\MPSP\Exceptions\TransferException;
use FintechFab\MPSP\Services\TransferService;
use Log;

/**
 * Class CancelJob
 *
 * Отмена денежнего перевода
 *
 * @package Monemobo\Queue\Jobs
 */
class CancelJob extends AbstractJob
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
		Log::info('CancelJob::run() Begin');

		$transferId = $requestData['transfer']['id'];
		$receiverNumber = $requestData['receiver']['phone'];

		try {
			Log::info('CancelJob::run() send a request to cancel', $requestData);
			$this->transfer->cancel($transferId, $receiverNumber);

		} catch (TransferException $exception) {

			Log::warning('CancelJob::run()', [
				'transfer_id' => $transferId,
				'error'       => $exception->getMessage(),
				'trace'       => $exception->getTraceAsString()
			]);

			$this->getAPIQueueInterface()->push('transferCancelResult', [
				'transfer_id' => $transferId,
				'error'       => [
					'code'    => $exception->getCode(),
					'message' => $exception->getMessage(),
				],
			]);

			// Не перевыставлять задачу
			return;
		}

		Log::info('CancelJob::run() Finished successfully');

		// ставим задачу в API с результатом
		$this->getAPIQueueInterface()->push('transferCancelResult', [
			'transfer_id' => $transferId,
		]);

		Log::info('CancelJob::run() Send job: transferCancelResult.', ['transfer_id' => $transferId]);
	}

}