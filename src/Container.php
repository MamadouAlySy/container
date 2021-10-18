<?php

declare(strict_types=1);

namespace MamadouAlySy;

use MamadouAlySy\Exceptions\MethodResolverException;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionParameter;

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

        $this->entries[$id] = $this->autoWire($id);

        return $this->entries[$id];
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
        $dependencies = $this->getDependencies($parameters);
        return $reflectedClass->newInstanceArgs($dependencies);
    }

    /**
     * @throws MethodResolverException|ReflectionException
     */
    public function resolveMethod(string $class, string $method): mixed
    {
        $object = $this->get($class);
        $reflectedClass = new ReflectionClass($object);
        if ($reflectedClass->hasMethod($method) && $reflectedClass->getMethod($method)->isPublic()) {
            $parameters = $reflectedClass->getMethod($method)->getParameters();
            $dependencies = $this->getDependencies($parameters);
            return call_user_func_array([$object, $method], $dependencies);
        }
        throw new MethodResolverException("can not resolve method $method make sure that it exists an it's public");
    }

    /**
     * @param ReflectionParameter[] $parameters
     * @return array
     * @throws ReflectionException
     */
    private function getDependencies(array $parameters): array
    {
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
        return $dependencies;
    }
}
