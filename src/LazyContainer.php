<?php

namespace Snapshotpl\LazyContainer;

use Psr\Container\ContainerInterface;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\Proxy\LazyLoadingInterface;

final class LazyContainer implements ContainerInterface
{
    private $container;
    private $proxyFactory;
    private $classMap;

    public function __construct(
        ContainerInterface $container,
        LazyLoadingValueHolderFactory $proxyFactory,
        array $classMap = []
    ) {
        $this->container = $container;
        $this->proxyFactory = $proxyFactory;
        $this->classMap = $classMap;
    }

    public function get($id)
    {
        if (!isset($this->classMap[$id])) {
            return $this->container->get($id);
        }

        $initializer = function (&$wrappedInstance, LazyLoadingInterface $proxy) use ($id) {
            $proxy->setProxyInitializer(null);
            $wrappedInstance = $this->container->get($id);
            return true;
        };

        return $this->proxyFactory->createProxy($this->classMap[$id], $initializer);
    }

    public function has($id)
    {
        return $this->container->has($id);
    }
}
