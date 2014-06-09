<?php namespace FintechFab\MPSP\Log;

use Monolog\Formatter\LineFormatter as MonologLineFormatter;

class LineFormatter extends MonologLineFormatter
{

	const SIMPLE_FORMAT = "[%datetime%] \033[0;32m%pid%\033[0m %channel%.%level_name%: %message% %context% %extra%\n";

	private $pid;

	public function __construct()
	{
		$this->pid = posix_getpid();
		parent::__construct();
	}

	public function format(array $record)
	{
		$output = parent::format($record);

		$output = str_replace('%pid%', $this->pid, $output);

		return $output;
	}

}