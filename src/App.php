<?php declare(strict_types=1);

namespace BearSys\WSF;

use BearSys\WSF\Brokers\IBroker;
use BearSys\WSF\Configuration\Configurator;
use BearSys\WSF\ConnectionManagers\IConnectionManager;
use BearSys\WSF\DI\Container;
use BearSys\WSF\Receivers\IReceiver;
use Composer\Autoload\ClassLoader;

class App extends \Ratchet\App
{
	/** @var Container */
	protected $dic;

	/** @var ClassLoader */
	protected $loader;

	/** @var array */
	protected $activeRoutes = [];


	public function injectDIC(Container $dic): void
	{
		$this->dic = $dic;
		$this->loader = $dic->get(ClassLoader::class);
	}


	public function setupRoutes(): void
	{
		/** @var Configurator $configurator */
		$configurator = $this->dic->get(Configurator::class);
		$configuration = $configurator->getConfiguration();

		foreach ($configuration->routes as $path => $receiver) {
			/** @var IReceiver $receiverInstance */
			$receiverInstance = $this->dic->get($receiver);
			/** @var IConnectionManager $conMgr */
			$conMgrInstance = $receiverInstance->getConnectionManager();

			if (substr($path, 0, 1) !== '/') {
				trigger_error('Route name should start with \'/\'!', E_USER_NOTICE);
				$path = '/' . $path;
			}

			$helper = new RouteHelper($receiverInstance, $conMgrInstance, $this->dic->get(IBroker::class));
			$this->route($path, $helper, $configuration->server->allowedOrigins ?: []);

			$this->activeRoutes[$path] = $receiver;
		}
	}
}