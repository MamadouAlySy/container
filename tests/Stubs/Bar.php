<?php

declare ( strict_types = 1 );

namespace MamadouAlySy\Tests\Stubs;

class Bar implements BarInterface
{
    public function __construct( protected Foo $foo ) {}
}
