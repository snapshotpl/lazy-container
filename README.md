# Lazy-Container [![Build Status](https://travis-ci.org/snapshotpl/lazy-container.svg?branch=master)](https://travis-ci.org/snapshotpl/lazy-container)
Lazy loading for interop container

Get lazy loadable object from any [interop container](https://github.com/container-interop/container-interop)! Powered by [Proxy Manager](https://github.com/Ocramius/ProxyManager)

## Usage

```php
// Build LazyLoadingValueHolderFactory as you want
$lazyLoadingFactory = new ProxyManager\Factory\LazyLoadingValueHolderFactory();

// Prepare you favorite container
$pimple = new Pimple\Container();
$pimple['service'] = function ($container) {
    return new HeavyService($container->get('dependency'));
};

// Create map (service name => class name) where you choose which services should be lazy loaded
$classMap = ['service' => HeavyService::class];

// Put all things to LazyContainer
$container = new LazyContainer($pimple, $lazyLoadingFactory, $classMap);

// Use LazyContainer exactly same like other interop container (thanks for interface)
$service = $container->get('service');

// Now $service is a proxy, so HeavyService wasn't created yet

// After first usage of $service is real HeavyService!
$result = $service->doSomething();
```

## Installation

You can install this package through Composer:

```
composer require snapshotpl/lazy-container
```
