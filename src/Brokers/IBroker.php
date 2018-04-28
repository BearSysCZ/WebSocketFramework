<?php declare(strict_types=1);

namespace BearSys\WSF\Brokers;

use BearSys\WSF\Handlers\IHandler;

interface IBroker
{
	public function send(string $channel, string $message, callable $callback = NULL): void;
	public function registerHandlers(\stdClass $routes, \stdClass $customHandlers): void;
	public function getChannelForHandler(IHandler $handler): string;
}