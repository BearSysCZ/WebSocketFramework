<?php declare(strict_types=1);

namespace BearSys\WSF\DI;

use BearSys\WSF\Exceptions\ClassNotFoundException;
use BearSys\WSF\Exceptions\InstanceAlreadyExistsException;
use BearSys\WSF\Exceptions\PrimitiveTypeInConstructorException;

class Container
{
	/** @var array */
	protected $instances = [];

	/** @var array */
	protected $implementations = [];


	public function __construct()
	{
		$this->instances[self::class] = $this;
	}


	public function get(string $type)
	{
		if (array_key_exists($type, $this->instances))
			return $this->instances[$type];
		elseif (array_key_exists($type, $this->implementations))
			return $this->implementations[$type];
		else
			return $this->create($type);
	}


	public function add($object): void
	{
		$type = get_class($object);
		if (array_key_exists($type, $this->instances))
			throw new InstanceAlreadyExistsException($type);

		$this->instances[$type] = $object;
		$this->loadInterfaces($object);
	}


	public function runFactories(\stdClass $configuration): void
	{
		if ($configuration->factories) {
			foreach ($configuration->factories as $factory) {
				/** @var IFactory $factoryInstance */
				$factoryInstance = $this->get($factory);
				$this->add($factoryInstance->create($configuration));
			}
		}
	}


	protected function create(string $type)
	{
		if (class_exists($type)) {
			$reflection = new \ReflectionClass($type);
			$constructor = $reflection->getConstructor();
			if ($constructor) {
				$constructorParams = $constructor->getParameters();
				$constructorInstances = [];

				foreach ($constructorParams as $param) {
					if ($class = $param->getClass())
						$constructorInstances[] = $this->get($class->getName());
					else
						throw new PrimitiveTypeInConstructorException($type);
				}

				$object = $reflection->newInstanceArgs($constructorInstances);
			} else
				$object = new $type;

			$this->instances[$type] = $object;
			$this->loadInterfaces($object);

			return $object;
		} else
			throw new ClassNotFoundException($type);
	}


	protected function loadInterfaces($object): void
	{
		$reflection = new \ReflectionClass($object);
		$interfaces = $reflection->getInterfaceNames();

		foreach ($interfaces as $interface) {
			if (array_key_exists($interface, $this->implementations)) {
				if (is_array($this->implementations[$interface]))
					$this->implementations[$interface][] = $object;
				else
					$this->implementations[$interface] = [
						$this->implementations[$interface],
						$object,
					];
			} else
				$this->implementations[$interface] = $object;
		}
	}
}