<?php

namespace MamadouAlySy\Tests;

use MamadouAlySy\Container;
use MamadouAlySy\Exceptions\NotFoundException;
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
}
