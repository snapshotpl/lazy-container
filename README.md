# Lazy-Container [![Build Status](https://travis-ci.org/snapshotpl/lazy-container.svg?branch=master)](https://travis-ci.org/snapshotpl/lazy-container)
Lazy loading container decorator

Get lazy loadable object from any interop container! Powered by [Proxy Manager](https://github.com/Ocramius/ProxyManager)!

## Usage

```php
// Build LazyLoadingValueHolderFactory as you want (remember to
$lazyLoadingFactory = new ProxyManager\Factory\LazyLoadingValueHolderFactory();

// Prepare you favorite container
$pimple = new Pimple\Container();
$pimple['service'] = function ($container) {
    return new HeavyService($container->get('dependency'));
};

// Create map (service name => class name) where you choose which services should be lazy loaded
$classMap = ['service' => HeavyService::class];

// Put all things to LazyContainer
$container = new LazyContainer(pimple, $lazyLoadingFactory, $classMap);

// Use LazyContainer exactly same like other interop container

$service = $container->get('service');

// Now $service is proxy so it's not created yet

$result = $service->doSomethig();

// now $service is real HeavyService!
```

## Installation

You can install this package through Composer:

```
composer require snapshotpl/lazy-container
```
