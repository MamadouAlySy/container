# Container

Version: 1.0.0

Github: <https://github.com/MamadouAlySy/container>

Author: Mamadou Aly Sy

## Description

This package is a simple php dependency container.

It can register object and retrieve them it can also autowire an object if it is not registered

## Installation

### Composer

    Install composer
    Type `composer require mamadou-aly-sy/container`
    Enjoy

## Methods

```php
get(string $id): object; // returns an entry
has(string $id): bool; // checks if an entry exist
register(string $id, ?callable $callable = null): void; // register an entry
autoWire(string $id): object; // autowire the given class
resolveMethod(string $class, string $method): mixed; // resolve method for the given class
save(object $object): void ; // save an object as an entry
```

## Usage

```php
# indented code
<?php

require_once './vendor/autoload.php';

$container = new \MamadouAlySy\Container();

$container->register(\MamadouAlySy\QueryBuilder::class, function() {
    return new \MamadouAlySy\QueryBuilder();
});

// this will return always the same instance of \MamadouAlySy\QueryBuilder::class
$container->get(\MamadouAlySy\QueryBuilder::class);
```
