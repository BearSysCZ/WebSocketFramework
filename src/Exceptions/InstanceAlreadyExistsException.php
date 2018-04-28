<?php declare(strict_types=1);

namespace BearSys\WSF\Exceptions;

class InstanceAlreadyExistsException extends \Exception
{
	public function __construct(string $class, $code = 0, \Throwable $previous = null)
	{
		parent::__construct('Instance of class "' . $class . '" already exists.', $code, $previous);
	}
}