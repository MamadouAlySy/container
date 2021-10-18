<?php

declare(strict_types=1);

namespace MamadouAlySy;

use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;

class Container implements ContainerInterface
{
    public function __construct(
        protected array $entries = []
    ) {
    }

    /**
     * @throws ReflectionException
     */
    public function get(string $id)
    {
        if ($this->has($id)) {
            return $this->entries[$id];
        }

        return $this->autoWire($id);
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->entries);
    }

    public function register(string $id, ?callable $callable = null)
    {
        $this->entries[$id] = $callable
            ? call_user_func_array($callable, [$this])
            : new $id;
    }

    /**
     * @throws ReflectionException
     */
    public function autoWire(string $id): object
    {
        $reflectedClass = new ReflectionClass($id);
        $constructor = $reflectedClass->getConstructor();
        if (is_null($constructor)) {
            return $reflectedClass->newInstanceWithoutConstructor();
        }
        $parameters = $constructor->getParameters();
        $dependencies = [];
        foreach ($parameters as $parameter) {
            if ($parameter->allowsNull()) {
                $dependencies[$parameter->getName()] = null;
            } elseif ($parameter->isDefaultValueAvailable()) {
                $dependencies[$parameter->getName()] = $parameter->getDefaultValue();
            } else {
                $dependencies[$parameter->getName()] = $this->get($parameter->getType()->getName());
            }
        }
        return $reflectedClass->newInstanceArgs($dependencies);
    }
}
