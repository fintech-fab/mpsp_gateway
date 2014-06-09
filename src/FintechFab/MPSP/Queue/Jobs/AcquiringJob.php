<?php namespace FintechFab\MPSP\Queue\Jobs;

use FintechFab\MPSP\Entities\Card;
use FintechFab\MPSP\Exceptions\AcquiringException;
use FintechFab\MPSP\Services\AcquiringService;

class AcquiringJob extends AbstractJob
{

	public function __construct(AcquiringService $acquiring, Card $card)
	{
		$this->acquiring = $acquiring;
		$this->card = $card;
		$this->apiQueue = $this->getAPIQueueInterface();
	}

	public function run(array $requestData)
	{
		$amount = $requestData['transfer']['amount'];
		$fee = $requestData['transfer']['fee'];
		$currency = $requestData['transfer']['currency'];

		$transferId = $requestData['transfer']['id'];

		// задаем данные для карты
		$this->card->doImport($requestData['card']);

		// снимаем деньги
		$amount += $fee;

		try {
			$result = $this->acquiring->doWithdraw($transferId, $this->card, $currency, $amount);

			// сообщаем в API о результате
			$this->apiQueue->push('acquiringResult', [
				'transfer_id'   => $transferId,
				'need_3ds'      => $result->isNeed3DS(),
				'3ds_url'       => $result->get3DSUrl(),
				'3ds_post_data' => $result->get3DSData(),
			]);

		} catch (AcquiringException $exception) {

			// сообщаем в API о результате
			$this->apiQueue->push('acquiringResult', [
				'transfer_id' => $transferId,
				'error'       => [
					'code'    => $exception->getCode(),
					'message' => $exception->getMessage(),
				],
				'need_3ds'    => false,
				'3ds_url'     => '',
			]);
		}
	}

}