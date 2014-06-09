<?php namespace FintechFab\MPSP\Services;

use FintechFab\MPSP\Services\Interfaces\TransferInterface;
use Log;

class TransferService
{
	/**
	 * @var TransferInterface
	 */
	private $transfer;

	public function __construct(Interfaces\TransferInterface $transfer)
	{
		$this->transfer = $transfer;
	}

	public function cities()
	{
		return $this->transfer->cities();
	}

	/**
	 * Выполнить проверку возможности перевода
	 *
	 * Полученный цифровой код следует передавать при запросе $this->request
	 *
	 * @param $transferId
	 * @param $data
	 *
	 * @return void
	 */
	public function check($transferId, array $data)
	{

		Log::info("TransferComponent::check() start", $data);

		if (empty($data['fee'])) {
			$data['fee'] = $this->fee($data['receiver']['city'], $data['transfer']['amount']);
		}

		$this->transfer->check($transferId, $data);

		Log::info("TransferComponent::check() complete", $data);
	}

	/**
	 * Вычислить стоимость комиссии для валюты $sCurrency
	 *
	 * @param int    $cityId
	 * @param string $amount
	 *
	 * @return float
	 */
	public function fee($cityId, $amount)
	{
		Log::info("TransferComponent::fee() start", ['city_id' => $cityId, 'amount' => $amount]);

		$resultAmount = $this->transfer->fee($cityId, $amount);

		Log::info("TransferComponent::fee() complete", ['city_id' => $cityId, 'amount' => $amount, 'result' => $resultAmount]);

		return $resultAmount;
	}

	/**
	 * Получить информацию о переводе
	 *
	 * @param int    $transferId Цифровой код (КНП)
	 * @param int    $checkNumber
	 * @param string $receiverNumber
	 *
	 * @see TransferStatus
	 *
	 * @return int
	 */
	public function status($transferId, $checkNumber, $receiverNumber)
	{
		return $this->transfer->status($transferId, $checkNumber, $receiverNumber);
	}

	/**
	 * Отменить перевод
	 *
	 * @param string $checkNumber
	 * @param string $receiverNumber
	 *
	 * @return bool
	 */
	public function cancel($checkNumber, $receiverNumber)
	{
		return $this->transfer->cancel($checkNumber, $receiverNumber);
	}

	/**
	 * Выполнить перевод
	 *
	 * @param int   $transferId
	 * @param array $data
	 *
	 * @return string Цифровой код
	 */
	public function pay($transferId, $data)
	{
		return $this->transfer->pay($transferId, $data);
	}

	/**
	 * Информация о последнем ответе провайдера
	 *
	 * @return string
	 */
	public function getLastResponse()
	{
		return $this->transfer->getLastResponse();
	}

	/**
	 * Информация о последнем запросе к серверу провайдера
	 *
	 * @return string
	 */
	public function getLastRequest()
	{
		return $this->transfer->getLastRequest();
	}

}