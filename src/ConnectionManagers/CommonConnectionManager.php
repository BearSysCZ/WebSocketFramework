<?php declare(strict_types=1);

namespace BearSys\WSF\ConnectionManagers;

use Ratchet\ConnectionInterface;

class CommonConnectionManager implements IConnectionManager
{
	/** @var \SplObjectStorage */
	protected $connections;


	public function __construct()
	{
		$this->connections = new \SplObjectStorage();
	}


	public function onOpen(ConnectionInterface $conn)
	{
		$this->connections->attach($conn);
	}


	public function onClose(ConnectionInterface $conn)
	{
		$this->connections->detach($conn);
	}


	public function onError(ConnectionInterface $conn, \Exception $e)
	{
		$this->connections->detach($conn);
	}


	public function getConnections()
	{
		$connections = [];
		foreach ($this->connections as $connection)
			$connections[] = $connection;
		return $connections;
	}
}