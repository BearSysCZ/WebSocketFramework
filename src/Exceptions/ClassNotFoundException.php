<?php declare(strict_types=1);

namespace BearSys\WSF\Exceptions;

class ClassNotFoundException extends \Exception
{
	public function __construct(string $className, $message = '', $code = 0, \Throwable $previous = null)
	{
		parent::__construct($message ?: 'Class "' . $className . '" not found. Is it autoloaded?', $code, $previous);
	}
}