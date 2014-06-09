<?php namespace FintechFab\MPSP\Queue\Jobs;

use FintechFab\MPSP\Constants\Status;
use FintechFab\MPSP\Services\TransferService;
use Log;

/**
 * Class StatusJob
 *
 * Проверка статуса денежного перевода. Если перевод находится в статусе ожидания получения денег,
 * то задача будет перевыставлена и запустится обработка через час
 *
 * Если перевод уже выплачен либо удалён - будет сгенерирован ответ в api с этим статусом
 *
 * @package Monemobo\Queue\Jobs
 */
class StatusJob extends AbstractJob
{

	/**
	 * @var TransferService
	 */
	private $transfer;

	public function __construct(TransferService $transfer)
	{
		$this->transfer = $transfer;
	}

	public function run(array $requestData)
	{
		$transferId = $requestData['transfer']['id'];
		$checkNumber = $requestData['transfer']['checknumber'];
		$receiverNumber = $requestData['receiver']['phone'];

		$status = $this->transfer->status($transferId, $checkNumber, $receiverNumber);

		switch ($status) {
			case Status::C_DELETE:
			case Status::C_PAID:
			case Status::C_NEW:

				$this->getAPIQueueInterface()->push('transferStatusResult', [
					'checknumber' => $checkNumber,
					'transfer_id' => $transferId,
					'status'      => $status,
				]);
				break;

			case Status::C_SEND:

				// Проверить статус через час
				Log::info("Перевод ожидает выдачи", $requestData);
				$this->job->release(36);
				break;

			default:
				Log::warning("Неизвестный статус: " . $status, $requestData);
		}
	}

}