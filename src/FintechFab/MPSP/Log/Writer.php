<?php namespace FintechFab\MPSP\Log;

use Illuminate\Log\Writer as LogWriter;
use Monolog\Handler\RotatingFileHandler;

class Writer extends LogWriter
{

	/**
	 * Register a daily file log handler.
	 *
	 * @param  string $path
	 * @param  int    $days
	 * @param  string $level
	 *
	 * @return void
	 */
	public function useDailyFiles($path, $days = 0, $level = 'debug')
	{
		$level = $this->parseLevel($level);

		$handler = new RotatingFileHandler($path, $days, $level);

		$handler->setFormatter(new LineFormatter());

		$this->monolog->pushHandler($handler);
	}

} 