<?php declare(strict_types=1);

namespace BearSys\WSF\Receivers;

use BearSys\WSF\ConnectionManagers\IConnectionManager;
use BearSys\WSF\Handlers\IHandler;
use Ratchet\ConnectionInterface;

interface IReceiver
{
	function onMessage(ConnectionInterface $from, $msg): IReceiverReturn;
	function getHandler(): IHandler;
	function getConnectionManager(): IConnectionManager;
}