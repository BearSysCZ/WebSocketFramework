<?php declare(strict_types=1);

namespace BearSys\WSF\Receivers;

class Forward implements IReceiverReturn
{
	/** @var string */
	protected $message;

	/** @var callable */
	protected $callback;


	public function __construct(string $message, callable $callback = NULL)
	{
		$this->message = $message;
		$this->callback = $callback;
	}


	public function getMessage(): string
	{
		return $this->message;
	}


	public function getCallback(): callable
	{
		return $this->callback;
	}
}