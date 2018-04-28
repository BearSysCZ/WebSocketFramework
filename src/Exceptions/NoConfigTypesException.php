<?php declare(strict_types=1);

namespace BearSys\WSF\Exceptions;

class NoConfigTypesException extends \Exception
{
	public function __construct($message = '', $code = 0, \Throwable $previous = null)
	{
		parent::__construct($message ?: 'There are no configuration type classes present.', $code, $previous);
	}
}