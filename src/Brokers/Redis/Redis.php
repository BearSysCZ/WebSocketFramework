<?php declare(strict_types=1);

namespace BearSys\WSF\Brokers\Redis;

use BearSys\WSF\Brokers\IBroker;
use BearSys\WSF\Configuration\Configurator;
use BearSys\WSF\DI\Container;
use BearSys\WSF\Exceptions\InvalidClassTypeException;
use BearSys\WSF\Handlers\IHandler;
use BearSys\WSF\Receivers\IReceiver;
use Clue\React\Redis\Factory;
use React\EventLoop\LoopInterface;

class Redis implements IBroker
{
	const DEFAULT_CONFIG = [
		'protocol' => 'redis',
		'host' => 'localhost',
		'port' => 6379,
		'dbIndex' => 0,
	];


	/** @var RedisClient */
	protected $publisher;

	/** @var RedisClient */
	protected $subscriber;

	/** @var Container */
	protected $dic;

	/** @var array */
	protected $handlers = [];


	public function __construct(Container $dic, LoopInterface $loop, Configurator $configurator)
	{
		$configuration = $configurator->getConfiguration();
		if (!$configuration->redis)
			$redisConfig = (object) self::DEFAULT_CONFIG;
		else
			$redisConfig = $configuration->redis;

		$this->publisher = $this->createClient($loop, $redisConfig, RedisClient::ROLE_PUBLISHER);
		$this->subscriber = $this->createClient($loop, $redisConfig, RedisClient::ROLE_SUBSCRIBER);
		$this->dic = $dic;

		$dic->add($this->publisher);
	}


	public function send(string $channel, string $message, callable $callback = NULL): void
	{
		$this->publisher->publish($channel, $message)->then($callback);
	}


	public function registerHandlers(\stdClass $routes, \stdClass $customHandlers): void
	{
		/** @var $channels array <string, IHandler> */
		$channels = [];

		foreach ($routes as $path => $handler) {
			$channels[$path] = $handler;
		}

		foreach ($customHandlers as $channel => $handler) {
			if (array_key_exists($channel, $channels))
				throw new \Exception('Channel "' . $channel . '" already in use by "' . get_class($channels[$channel]) . '".');
			$handler = $this->dic->get($handler);
			$channels[$channel] = $handler;
		}

		foreach ($channels as $channel => $handler) {
			if (!$handler instanceof IHandler)
				throw new InvalidClassTypeException(IHandler::class, get_class($handler));

			$this->subscriber->subscribe($channel);
			$this->handlers[get_class($handler)] = $channel;
		}

		$this->subscriber->on('message', function ($channel, $message) use ($channels) {
			$channels[$channel]->onMessage($message);
		});
	}


	public function getChannelForHandler(IHandler $handler): string
	{
		return $this->handlers[get_class($handler)];
	}


	protected function createClient(LoopInterface $loop, \stdClass $redisConfig, string $role): RedisClient
	{
		$factory = new Factory($loop);
		$target = ($redisConfig->protocol ?: 'redis') . '://' . ($redisConfig->host ?: 'localhost') . ':' . ($redisConfig->port ?: 6379) . '/' . ($redisConfig->dbIndex ?: 0);
		$promise = $factory->createClient($target);
		return new RedisClient($promise, $redisConfig, $role);
	}
}