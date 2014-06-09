<?php namespace FintechFab\MPSP\Queue\Jobs;

use FintechFab\MPSP\Sms\SmsInterface;

class SmsJob extends AbstractJob
{

	/**
	 * @var \FintechFab\MPSP\Sms\SmsInterface
	 */
	private $sms;

	public function __construct(SmsInterface $sms)
	{
		$this->sms = $sms;
	}

	public function run(array $requestData)
	{
		$this->sms->send($requestData['phone'], $requestData['message']);
	}

} 