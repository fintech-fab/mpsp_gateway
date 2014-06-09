<?php namespace FintechFab\MPSP\Sms\Gates;

use FintechFab\MPSP\Sms\SmsInterface;

class MailGate implements SmsInterface
{

	private $mailTo = '';

	public function __construct($config)
	{
		$this->mailTo = $config['email'];
	}

	public function send($phone, $message)
	{
		mail($this->mailTo, $message, $phone);
	}

} 