<?php

namespace Snapshotpl\LazyContainer\Test;

use ArrayObject;
use PHPUnit\Framework\TestCase;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use Snapshotpl\LazyContainer\LazyContainer;
use Snapshotpl\NanoContainer\NanoContainer;

class LazyContainerTest extends TestCase
{
    protected $container;
    protected $decoratedContainer;
    protected $loaded;

    protected function setUp()
    {
        $this->loaded = null;
        $factories = [
            'foo' => function($container, $id) {
                $this->loaded = $id;

                return new ArrayObject();
            },
            'bar' => function ($container, $id) {
                $this->loaded = $id;

                return new ArrayObject();
            },
        ];

        $this->decoratedContainer = new NanoContainer($factories);
        $lazyLoadingFactory = new LazyLoadingValueHolderFactory();
        $classMap = ['foo' => ArrayObject::class];

        $this->container = new LazyContainer($this->decoratedContainer, $lazyLoadingFactory, $classMap);
    }

    public function testGetExpectedObjectFromContainer()
    {
        $result = $this->container->get('foo');

        $this->assertInstanceOf(ArrayObject::class, $result);
    }

    public function testInstanceIsNotCreatedBeforeUsage()
    {
        $this->container->get('foo');

        $this->assertFalse($this->isInstanceCreated('foo'));
    }

    public function testInstanceIsCreatedAfterUsage()
    {
        $this->container->get('foo')->ksort();

        $this->assertTrue($this->isInstanceCreated('foo'));
    }

    public function testInstanceIsCreatedBeforeUsageIfNotMapped()
    {
        $this->container->get('bar');

        $this->assertTrue($this->isInstanceCreated('bar'));
    }

    public function testCheckServiceExistsWorksSameAsDecoratedContainer()
    {
        $result = $this->container->has('foo');

        $this->assertTrue($result);
    }

    protected function isInstanceCreated($serviceId)
    {
        return $this->loaded === $serviceId;
    }
}
