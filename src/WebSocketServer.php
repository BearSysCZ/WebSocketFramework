<?php declare(strict_types=1);

namespace BearSys\WSF;

use BearSys\WSF\Configuration\Configurator;
use BearSys\WSF\Configuration\Types\IType;
use BearSys\WSF\DI\Container;
use Composer\Autoload\ClassLoader;
use React\EventLoop\LoopInterface;

class WebSocketServer
{
	/** @var LoopInterface */
	protected $loop;

	/** @var ClassLoader */
	protected $loader;

	/** @var Container */
	protected $dic;

	/** @var Configurator */
	protected $configurator;


	public function __construct(LoopInterface $loop, ClassLoader $loader)
	{
		$this->loop = $loop;
		$this->loader = $loader;
		$this->configurator = $configurator = new Configurator($loader);
		$this->dic = new Container;

		$this->dic->add($loop);
		$this->dic->add($loader);
		$this->dic->add($configurator);
		$this->dic->runFactories($configurator->getConfiguration());
	}


	public function run(): void
	{
		$configuration = $this->configurator->getConfiguration();

		$app = new App($configuration->server->host ?: 'localhost', $configuration->server->port ?: '8080', $configuration->server->address ?: '0.0.0.0', $this->loop);
		$app->injectDIC($this->dic);
		$app->setupRoutes();

		$broker = $this->dic->get($configuration->broker->type);
		$routesHandlers = [];
		foreach ($configuration->routes as $route => $receiver) {
			$receiver = $this->dic->get($receiver);
			$handler = $receiver->getHandler();
			$routesHandlers[$route] = $handler;
		}
		$broker->registerHandlers((object) $routesHandlers, $configuration->broker->customHandlers ?: (object) []);

		$app->run();
	}


	public function addConfigType(IType $type): void
	{
		$this->configurator->addConfigType($type);
	}


	public function addConfig(string $fileName): void
	{
		$this->configurator->addConfig($fileName);
	}
}