<?php declare(strict_types=1);

namespace BearSys\WSF\Exceptions;

class InvalidClassTypeException extends \Exception
{
	public function __construct(string $requiredType, string $class, $code = 0, \Throwable $previous = null)
	{
		parent::__construct('Required class of type "' . $requiredType . '". "' . $class . '" given.', $code, $previous);
	}
}