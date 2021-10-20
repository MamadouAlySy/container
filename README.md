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

## Usage

```php
<?php

require_once './vendor/autoload.php';

$container = new \MamadouAlySy\Container();

$container->set(\MamadouAlySy\QueryBuilder::class, function() {
    return new \MamadouAlySy\QueryBuilder();
});
// or set interface
$container->set(
    \MamadouAlySy\QueryBuilderInterface::class, 
    \MamadouAlySy\QueryBuilder::class
);
// this will return an instance of \MamadouAlySy\QueryBuilder::class
$container->get(\MamadouAlySy\QueryBuilder::class);
```
