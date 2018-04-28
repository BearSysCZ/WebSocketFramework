<?php declare(strict_types=1);

namespace BearSys\WSF\Receivers;

use BearSys\WSF\Brokers\IBroker;
use BearSys\WSF\ConnectionManagers\IConnectionManager;
use BearSys\WSF\DI\Container;
use BearSys\WSF\Exceptions\InvalidClassTypeException;
use BearSys\WSF\Handlers\IHandler;

abstract class BaseReceiver implements IReceiver
{
	/** @var Container */
	protected $dic;

	/** @var IBroker */
	protected $broker;


	public function __construct(Container $dic)
	{
		$this->dic = $dic;
		$this->broker = $dic->get(IBroker::class);
	}


	public function getHandler(): IHandler
	{
		$handlerFQN = substr(get_class($this), 0, -8) . 'Handler';
		$handler = $this->dic->get($handlerFQN);

		if (!$handler instanceof IHandler)
			throw new InvalidClassTypeException(IHandler::class, $handlerFQN);

		return $handler;
	}


	public function getConnectionManager(): IConnectionManager
	{
		$conMgrFQN = substr(get_class($this), 0, -8) . 'ConnectionManager';
		$conMgr = $this->dic->get($conMgrFQN);

		if (!$conMgr instanceof IConnectionManager)
			throw new InvalidClassTypeException(IConnectionManager::class, $conMgrFQN);

		return $conMgr;
	}
}