<?php declare(strict_types=1);

namespace BearSys\WSF;

use BearSys\WSF\Brokers\IBroker;
use BearSys\WSF\ConnectionManagers\IConnectionManager;
use BearSys\WSF\Receivers\Forward;
use BearSys\WSF\Receivers\IReceiver;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class RouteHelper implements MessageComponentInterface
{
	/** @var IReceiver */
	protected $receiver;

	/** @var IConnectionManager */
	protected $conMgr;

	/** @var IBroker */
	protected $broker;


	public function __construct(IReceiver $receiver, IConnectionManager $conMgr, IBroker $broker)
	{
		$this->receiver = $receiver;
		$this->conMgr = $conMgr;
		$this->broker = $broker;
	}

	public function onOpen(ConnectionInterface $conn): void
	{
		$this->conMgr->onOpen($conn);
	}

	public function onClose(ConnectionInterface $conn): void
	{
		$this->conMgr->onClose($conn);
	}

	public function onError(ConnectionInterface $conn, \Exception $e): void
	{
		$this->conMgr->onError($conn, $e);
	}

	public function onMessage(ConnectionInterface $from, $msg): void
	{
		$return = $this->receiver->onMessage($from, $msg);

		if ($return instanceof Forward)
			$this->broker->send($this->broker->getChannelForHandler($this->receiver->getHandler()), $return->getMessage(), $return->getCallback());
	}
}