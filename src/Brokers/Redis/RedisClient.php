<?php declare(strict_types=1);

namespace BearSys\WSF\Brokers\Redis;

use Clue\React\Redis\Client;
use React\Promise\PromiseInterface;

class RedisClient
{
	const ROLE_PUBLISHER = 'publisher';
	const ROLE_SUBSCRIBER = 'subscriber';

	/** @var PromiseInterface */
	protected $promise;

	/** @var string */
	protected $role;


	public function __construct(PromiseInterface $promise, \stdClass $redisConfig, string $role)
	{
		$this->promise = $promise;

		$this->enableNotifications($redisConfig->notifyKeyspaceEvents);

		if (!($role === self::ROLE_PUBLISHER || $role === self::ROLE_SUBSCRIBER))
			throw new \Exception('Invalid role for Redis instance.');

		$this->role = $role;
	}


	public function enableNotifications($level): PromiseInterface
	{
		return $this->promise->then(function (Client $client) use ($level) {
			$client->config('set', 'notify-keyspace-events', $level);
		});
	}


	public function done(callable $callback): PromiseInterface
	{
		return $this->promise->then($callback);
	}


	public function on($event, callable $callback): PromiseInterface
	{
		return $this->promise->then(function (Client $client) use ($event, $callback) {
			$client->on($event, $callback);
		});
	}


	public function subscribe($channel): PromiseInterface
	{
		return $this->promise->then(function (Client $client) use ($channel) {
			$client->subscribe($channel);
		});
	}


	public function publish($channel, $data): PromiseInterface
	{
		return $this->promise->then(function (Client $client) use ($channel, $data) {
			$client->publish($channel, $data);
		});
	}


	public function get($key, callable $callback): PromiseInterface
	{
		return $this->promise->then(function (Client $client) use ($key, $callback) {
			$client->get($key)->done($callback);
		});
	}


	public function set($key, $value): PromiseInterface
	{
		return $this->promise->then(function (Client $client) use ($key, $value) {
			$client->set($key, $value);
		});
	}


	public function del($key): PromiseInterface
	{
		return $this->promise->then(function (Client $client) use ($key) {
			$client->del($key);
		});
	}


	public function incr($key): PromiseInterface
	{
		return $this->promise->then(function (Client $client) use ($key) {
			$client->incr($key);
		});
	}


	public function decr($key): PromiseInterface
	{
		return $this->promise->then(function (Client $client) use ($key) {
			$client->decr($key);
		});
	}


	public function expire($key, $seconds): PromiseInterface
	{
		return $this->promise->then(function (Client $client) use ($key, $seconds) {
			$client->expire($key, $seconds);
		});
	}
}