<?php

declare(strict_types=1);

namespace MamadouAlySy;

use JetBrains\PhpStorm\Pure;
use MamadouAlySy\Exceptions\ContainerException;
use MamadouAlySy\Exceptions\NotFoundException;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionUnionType;

class Container implements ContainerInterface
{
    private array $entries = [];

    /**
     * @inheritDoc
     */
    public function get(string $id)
    {
        if ($this->has($id)) {
            $entry = $this->entries[$id];
            if (is_callable($entry)) {
                return call_user_func_array($entry, [$this]);
            }
            $id = $entry;
        }

        if (!class_exists($id)) {
            throw new NotFoundException("No entry was found for $id");
        }

        try {
            return $this->resolve($id);
        } catch (ReflectionException $e) {
            throw new ContainerException($e->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function has(string $id): bool
    {
        return array_key_exists($id, $this->entries);
    }

    /**
     * @param string $id
     * @param callable|string $resolver
     */
    public function set(string $id, callable|string $resolver): void
    {
        $this->entries[$id] = $resolver;
    }

    /**
     * @param string $id
     * @return object
     * @throws ContainerException
     * @throws ReflectionException
     */
    public function resolve(string $id): object
    {
        // 1. Inspect the class that we are trying to get from the container
        $reflectionClass = new ReflectionClass($id);
        if (!$reflectionClass->isInstantiable()) {
            throw new ContainerException("Class $id is not instantiable.");
        }

        // 2. Inspect the constructor of the class
        $constructor = $reflectionClass->getConstructor();
        if (!$constructor) {
            return $reflectionClass->newInstanceWithoutConstructor();
        }

        // 3. Inspect the constructor parameters (dependencies)
        $parameters = $constructor->getParameters();
        if (!$parameters) {
            return $reflectionClass->newInstance();
        }

        // 4. If the constructor parameter is a class try to resole that class from the container
        $dependencies = $this->resolveDependencies($parameters);
        
        return $reflectionClass->newInstanceArgs($dependencies);
    }

    /**
     * @param ReflectionParameter[] $parameters
     * @return object[]
     * @throws ContainerException
     */
    public function resolveDependencies(array $parameters): array
    {
        return array_map(function (ReflectionParameter $param) {

            $this->verifyParameter($param);

            list($name, $type, $parentClass) = $this->getParameterInfo($param);

            if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                return $this->get($type->getName());
            }

            throw new ContainerException(
                "Failed to resolve class $parentClass because of invalid param $name"
            );

        }, $parameters);
    }

    /**
     * @param ReflectionParameter $param
     * @throws ContainerException
     */
    public function verifyParameter(ReflectionParameter $param): void
    {
        list($name, $type, $parentClass) = $this->getParameterInfo($param);

        if (!$type) {
            throw new ContainerException(
                "Failed to resolve class $parentClass because param $name is missing a type hint."
            );
        }

        if ($type instanceof ReflectionUnionType) {
            throw new ContainerException(
                "Failed to resolve class $parentClass because of union type for param $name."
            );
        }
    }


    /**
     * @param ReflectionParameter $param
     * @return array[name, type, parentClass]
     */
    #[Pure]
    public function getParameterInfo(ReflectionParameter $param): array
    {
        return [
            $param->getName(),
            $param->getType(),
            $param->getDeclaringClass()->getName()
        ];
    }
}
