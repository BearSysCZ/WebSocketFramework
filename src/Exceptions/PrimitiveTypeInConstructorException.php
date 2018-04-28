<?php declare(strict_types=1);

namespace BearSys\WSF\Exceptions;

class PrimitiveTypeInConstructorException extends \Exception
{
	public function __construct(string $class, $code = 0, \Throwable $previous = null)
	{
		parent::__construct('Constructor of the class "' . $class . '" requires primitive type argument. Use factory to create instance of this class.', $code, $previous);
	}
}