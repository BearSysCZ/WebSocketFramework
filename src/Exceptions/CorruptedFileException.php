<?php declare(strict_types=1);

namespace BearSys\WSF\Exceptions;

class CorruptedFileException extends \Exception
{
	public function __construct($message = '', $code = 0, \Throwable $previous = null)
	{
		parent::__construct($message ?: 'Given file cannot be read. Check if it is not corrupted or inaccessible.', $code, $previous);
	}
}