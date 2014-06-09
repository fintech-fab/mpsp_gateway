<?php namespace FintechFab\MPSP\Services\Interfaces;

interface AcquiringResultInterface
{

	/**
	 * Требуется пройти 3DS?
	 *
	 * @return bool
	 */
	public function isNeed3DS();

	/**
	 * Получить ссылку на 3DS
	 *
	 * @return string
	 */
	public function get3DSUrl();

	/**
	 * Получить данные по 3DS
	 *
	 * @return array
	 */
	public function get3DSData();

	/**
	 * Средства списаны успешно?
	 *
	 * @return bool
	 */
	public function isSuccess();

	/**
	 * Получить параметры ответа
	 *
	 * @return array
	 */
	public function getResponseData();

} 