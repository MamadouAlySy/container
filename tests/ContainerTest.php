<?php

namespace MamadouAlySy\Tests;

use MamadouAlySy\Container;
use MamadouAlySy\Tests\Stubs\A;
use MamadouAlySy\Tests\Stubs\B;
use MamadouAlySy\Tests\Stubs\C;
use PHPUnit\Framework\TestCase;

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
        $this->container->register(A::class);

        $this->assertInstanceOf(
            expected: A::class,
            actual: $this->container->get(A::class)
        );
    }

    public function testCanRegisterAnEntryWithCallableAnGetIt()
    {
        $this->container->register(B::class, fn () => new B(new A));

        $this->assertInstanceOf(
            expected: B::class,
            actual: $this->container->get(B::class)
        );
    }

    public function testCanGetAnEntryByAutoWire()
    {
        $this->assertInstanceOf(
            expected: C::class,
            actual: $this->container->get(C::class)
        );
    }
}
