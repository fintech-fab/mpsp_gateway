<?php namespace FintechFab\MPSP\Services\Gates\Results;

use FintechFab\MPSP\Services\Interfaces\AcquiringResultInterface;

class EmulatorAcquiringResult implements AcquiringResultInterface
{

	public function __construct($success = false, array $responseData = null, $is3DS = false, $threedUrl = null, array $threedData = null)
	{
		$this->success = $success;
		$this->responseData = $responseData;
		$this->is3DS = $is3DS;
		$this->threedUrl = $threedUrl;
		$this->threedData = $threedData;
	}

	/**
	 * Требуется пройти 3DS?
	 *
	 * @return bool
	 */
	public function isNeed3DS()
	{
		return $this->is3DS;
	}

	/**
	 * Получить ссылку на 3DS
	 *
	 * @return string
	 */
	public function get3DSUrl()
	{
		return $this->threedUrl;
	}

	/**
	 * Получить данные по 3DS
	 *
	 * @return array
	 */
	public function get3DSData()
	{
		return $this->threedData;
	}

	/**
	 * Средства списаны успешно?
	 *
	 * @return bool
	 */
	public function isSuccess()
	{
		return $this->success;
	}

	/**
	 * Получить параметры ответа
	 *
	 * @return array
	 */
	public function getResponseData()
	{
		return $this->responseData;
	}
}