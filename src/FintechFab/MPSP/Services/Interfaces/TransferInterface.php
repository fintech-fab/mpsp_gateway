<?php namespace FintechFab\MPSP\Services\Interfaces;

interface TransferInterface
{

	/**
	 * Получить список городов
	 *
	 * @return array
	 */
	public function cities();

	/**
	 * Получить значение комиссии
	 *
	 * @param int   $cityId
	 * @param float $amount
	 *
	 * @return float
	 */
	public function fee($cityId, $amount);

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
	public function check($transferId, $data);

	/**
	 * Получить статус перевода
	 *
	 * @param int    $transferId Цифровой код
	 * @param int    $checkNumber
	 * @param string $receiverNumber
	 *
	 * @see TransferStatus
	 *
	 * @return int
	 */
	public function status($transferId, $checkNumber, $receiverNumber);

	/**
	 * Отменить перевод
	 *
	 * @param string $checkNumber
	 * @param string $receiverNumber
	 *
	 * @return bool
	 */
	public function cancel($checkNumber, $receiverNumber);

	/**
	 * Выполнить перевод
	 *
	 * @param int   $transferId
	 * @param array $data
	 *
	 * @return string Цифровой код
	 */
	public function pay($transferId, $data);

	/**
	 * Информация о последнем ответе провайдера
	 *
	 * @return string
	 */
	public function getLastRequest();

	/**
	 * Информация о последнем запросе к серверу провайдера
	 *
	 * @return string
	 */
	public function getLastResponse();

}

