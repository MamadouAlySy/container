<?php

namespace MamadouAlySy\Tests;

use MamadouAlySy\Container;
use MamadouAlySy\Tests\Stubs\A;
use MamadouAlySy\Tests\Stubs\B;
use MamadouAlySy\Tests\Stubs\C;
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

    /**
     * @throws ReflectionException
     */
    public function testCanRegisterAnEntryAnGetIt()
    {
        $this->container->register(A::class);

        $this->assertInstanceOf(
            expected: A::class,
            actual: $this->container->get(A::class)
        );
    }

    /**
     * @throws ReflectionException
     */
    public function testCanRegisterAnEntryWithCallableAnGetIt()
    {
        $this->container->register(B::class, fn () => new B(new A));

        $this->assertInstanceOf(
            expected: B::class,
            actual: $this->container->get(B::class)
        );
    }

    /**
     * @throws ReflectionException
     */
    public function testCanGetAnEntryByAutoWire()
    {
        $this->assertInstanceOf(
            expected: C::class,
            actual: $this->container->get(C::class)
        );
    }

    /**
     * @throws ReflectionException
     */
    public function testCanSaveAnEntryAfterAutoWire()
    {
        $this->container->get(C::class);
        $this->assertTrue(condition: $this->container->has(C::class));
    }
}
