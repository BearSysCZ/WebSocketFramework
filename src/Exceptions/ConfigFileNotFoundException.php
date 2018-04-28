<?php declare(strict_types=1);

namespace BearSys\WSF\Exceptions;

class ConfigFileNotFoundException extends \Exception
{
	public function __construct($message = '', $code = 0, \Throwable $previous = null)
	{
		parent::__construct($message ?: 'Configuration file not found. Did you enter correct name or path?', $code, $previous);
	}
}