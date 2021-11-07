<?php

namespace MamadouAlySy\Tests;

use MamadouAlySy\Container;
use MamadouAlySy\Exceptions\ContainerException;
use MamadouAlySy\Tests\Stubs\Bar;
use MamadouAlySy\Tests\Stubs\BarInterface;
use MamadouAlySy\Tests\Stubs\Foo;
use PHPUnit\Framework\TestCase;
use ReflectionException;

class ContainerTest extends TestCase
{
    protected Container $container;

    protected function setUp(): void
    {
        parent::setUp();
        $this->container = new Container();
    }

    public function testCanRegisterAnEntryAnGetIt()
    {
        $this->container->set(Foo::class, fn() => new Foo());

        $this->assertInstanceOf(
            expected: Foo::class,
            actual: $this->container->get(Foo::class)
        );
    }

    public function testCanRegisterAnInterface()
    {
        $this->container->set(BarInterface::class, Bar::class);

        $this->assertInstanceOf(
            expected: Bar::class,
            actual: $this->container->get(BarInterface::class)
        );
    }

    public function testCanRegisterAnEntryAnGetItButWillAlwaysReturnNewInstance()
    {
        $this->container->set(BarInterface::class, fn() => new Foo());

        $foo1 = $this->container->get(BarInterface::class);
        $foo2 = $this->container->get(BarInterface::class);

        $this->assertNotSame(
            expected: spl_object_id($foo1),
            actual: spl_object_id($foo2)
        );
    }

    /**
     * @throws ContainerException
     * @throws ReflectionException
     */
    public function testCanRegisterAnEntryAsSingletonAnGetIt()
    {
        $this->container->makeSingleton(BarInterface::class, fn() => new Foo());

        $foo1 = $this->container->get(BarInterface::class);
        $foo2 = $this->container->get(BarInterface::class);

        $this->assertSame(
            expected: spl_object_id($foo1),
            actual: spl_object_id($foo2)
        );
    }
}
