<?php namespace FintechFab\MPSP\Sms;

interface SmsInterface
{

	/**
	 * @param $phone
	 * @param $message
	 *
	 * @return bool
	 */
	public function send($phone, $message);

} 