<?php

namespace MamadouAlySy\Tests;

use MamadouAlySy\Container;
use MamadouAlySy\Exceptions\MethodResolverException;
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

    /**
     * @throws ReflectionException|MethodResolverException
     */
    public function testCanResolveMethod()
    {
        $this->assertEquals(
            expected: 'success',
            actual: $this->container->resolveMethod(C::class, 'process')
        );
    }

    /**
     * @throws ReflectionException|MethodResolverException
     */
    public function testWillThrowMethodResolverException()
    {
        $this->expectException(MethodResolverException::class);
        $this->container->resolveMethod(C::class, 'unknown');
    }
    
    public function testCanSaveAnObjectAsAnEntry()
    {
        $this->container->save(new A);
        $this->assertTrue($this->container->has(A::class));
    }
}
