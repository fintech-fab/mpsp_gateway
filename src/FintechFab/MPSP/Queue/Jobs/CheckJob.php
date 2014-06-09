<?php namespace FintechFab\MPSP\Queue\Jobs;

use Exception;
use FintechFab\MPSP\Exceptions\TransferException;
use FintechFab\MPSP\Services\TransferService;
use Log;

class CheckJob extends AbstractJob
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
		Log::info('CheckJob::run() Begin');

		$transferId = $requestData['transfer']['id'];

		try {
			Log::info('CheckJob::run() send a request to check', $requestData);

			$this->transfer->check($transferId, $requestData);
		} catch (TransferException $exception) {

			// Система ответила что не может создать запрос с этими параметрами
			Log::warning('CheckJob::run()', [
				'transfer_id' => $transferId,
				'error'       => $exception->getMessage(),
				'trace'       => $exception->getTraceAsString()
			]);

			$this->getAPIQueueInterface()->push('transferCheckResult', [
				'transfer_id' => $transferId,
				'error'       => [
					'code'    => $exception->getCode(),
					'message' => $exception->getMessage(),
				],
			]);

			// Не перевыставлять задачу
			return;
		}

		Log::info('CheckJob::run() Finished successfully');

		// ставим задачу в API с результатом
		$this->getAPIQueueInterface()->push('transferCheckResult', [
			'transfer_id' => $transferId,
		]);

		Log::info('CheckJob::run() Send job: transferCheckResult.', ['transfer_id' => $transferId]);
	}

	public function failure($data, Exception $exception = null)
	{
		Log::info('CheckJob::failure() Отмена запроса на перевод денег');

		$transferId = $data['transfer']['id'];

		$this->getAPIQueueInterface()->push('transferCheckResult', [
			'transfer_id' => $transferId,
			'error'       => [
				'code'    => -1,
				'message' => $exception->getMessage(),
			],
		]);
	}

}