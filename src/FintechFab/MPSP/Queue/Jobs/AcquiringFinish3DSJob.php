<?php namespace FintechFab\MPSP\Queue\Jobs;

use FintechFab\MPSP\Exceptions\AcquiringException;
use FintechFab\MPSP\Services\AcquiringService;

class AcquiringFinish3DSJob extends AbstractJob
{

	private $apiQueue, $acquiring;

	public function __construct(AcquiringService $acquiring)
	{
		$this->acquiring = $acquiring;
		$this->apiQueue = $this->getAPIQueueInterface();
	}

	public function run(array $requestData)
	{
		// обрабатываем 3DS
		try {
			$this->acquiring->doFinish3DS($requestData['3ds_data']);

			$this->apiQueue->push('acquiringResult', [
				'transfer_id'   => $requestData['transfer']['id'],
				'need_3ds'      => false,
				'3ds_url'       => '',
				'3ds_post_data' => '',
			]);

		} catch (AcquiringException $exception) {

			// сообщаем в API о результате
			$this->apiQueue->push('acquiringResult', [
				'transfer_id' => $requestData['transfer']['id'],
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