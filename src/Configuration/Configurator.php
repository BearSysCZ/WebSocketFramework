<?php declare(strict_types=1);

namespace BearSys\WSF\Configuration;

use BearSys\WSF\Configuration\Types\IType;
use BearSys\WSF\Exceptions\ConfigFileNotFoundException;
use BearSys\WSF\Exceptions\CorruptedFileException;
use BearSys\WSF\Exceptions\NoConfigTypesException;
use Composer\Autoload\ClassLoader;

class Configurator
{
	/** @var array */
	protected $configs = [];

	/** @var array of IType instance FQNs */
	protected $types;

	/** @var \stdClass */
	protected $configuration;


	public function __construct(ClassLoader $loader)
	{
		if (!$this->loadTypes($loader))
			throw new NoConfigTypesException;
	}


	public function addConfigType(IType $type): void
	{
		array_unshift($this->types, $type); // Overrides built-in implementation
	}


	public function addConfig(string $fileName): void
	{
		if (file_exists($fileName))
			$this->configs[] = $fileName;
		else
			throw new ConfigFileNotFoundException;
	}


	public function getConfiguration(): \stdClass
	{
		if (!$this->configuration)
			$this->configuration = $this->parseConfigs();

		return $this->configuration;
	}


	protected function parseConfigs(): \stdClass
	{
		$parsed = [];
		foreach ($this->configs as $config) {
			$parsed = array_merge($parsed, $this->parseConfig($config));
		}

		$this->checkMandatoryOptions($parsed);

		return json_decode(json_encode($parsed));
	}


	protected function parseConfig(string $config): array
	{
		$extension = substr($config, strrpos($config, '.', -5) + 1);
		$content = file_get_contents($config);

		if ($content) {
			/** @var IType $typeClass */
			foreach ($this->types as $typeClass) {
				if (in_array($extension, $typeClass::getExtensionList()))
					return $typeClass::parse($content);
			}
			throw new CorruptedFileException;
		} else {
			throw new CorruptedFileException;
		}
	}


	protected function loadTypes(ClassLoader $loader): bool
	{
		// dynamically loads built-in types
		$types = array_diff(scandir(dirname($loader->findFile(IType::class))), ['.', '..', 'IType.php']);

		$typeClassFQNs = [];
		foreach ($types as $type) {
			$typeClassFQNs[] = substr(IType::class, 0, -5) . substr($type, 0, -4);
		}

		if (count($typeClassFQNs) > 0) {
			$this->types = $typeClassFQNs;
			return TRUE;
		} else {
			return FALSE;
		}
	}


	protected function checkMandatoryOptions(array $config): void
	{
		$missing = [];

		if (!array_key_exists('broker', $config))
			$missing[] = 'broker';
		elseif (array_key_exists('type', $config['broker']))
			$missing[] = 'broker->type';

		if (!array_key_exists('server', $config))
			$missing[] = 'server';
		else {
			if (!array_key_exists('host', $config['server']))
				$missing[] = 'server->host';
			if (!array_key_exists('port', $config['server']))
				$missing[] = 'server->port';
		}

		if (!array_key_exists('routes', $config))
			$missing[] = 'routes';
		elseif (count($config['routes']))
			throw new \Exception();

		if (count($missing))
			throw new \Exception();
	}
}