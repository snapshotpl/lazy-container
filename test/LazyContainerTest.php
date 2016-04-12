<?php

namespace Snapshotpl\LazyContainer\Test;

use ArrayObject;
use Interop\Container\ContainerInterface;
use PHPUnit_Framework_TestCase;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use Snapshotpl\LazyContainer\LazyContainer;

class LazyContainerTest extends PHPUnit_Framework_TestCase
{
    protected $container;
    protected $decoratedContainer;

    protected function setUp()
    {
        $this->decoratedContainer = $this->getMock(ContainerInterface::class);
        $lazyLoadingFactory = new LazyLoadingValueHolderFactory();
        $classMap = ['foo' => ArrayObject::class];

        $this->container = new LazyContainer($this->decoratedContainer, $lazyLoadingFactory, $classMap);
    }

    public function testGetExpectedObjectFromContainer()
    {
        $this->decoratedContainer->method('get')->with('foo')->willReturn(new ArrayObject());

        $result = $this->container->get('foo');

        $this->assertInstanceOf(ArrayObject::class, $result);
    }

    public function testInstanceIsNotCreatedBeforeUsage()
    {
        $this->assertFalse($this->isLazyLoaded('foo'));
    }

    public function testInstanceIsCreatedAfterUsage()
    {
        $this->assertTrue($this->isLazyLoaded('foo', function ($object) {
            $object->ksort();
        }));
    }

    public function testInstanceIsCreatedBeforeUsageIfNotMapped()
    {
        $this->assertTrue($this->isLazyLoaded('bar'));
    }

    public function testCheckServiceExistsWorksSameAsDecoratedContainer()
    {
        $this->decoratedContainer->method('has')->with('boo')->willReturn(true);

        $result = $this->container->has('boo');

        $this->assertTrue($result);
    }

    protected function isLazyLoaded($name, callable $proxyUsage = null)
    {
        $isLoaded = false;
        $this->decoratedContainer->method('get')->with($name)->willReturnCallback(function() use (&$isLoaded) {
            $isLoaded = true;
            return new ArrayObject();
        });

        $result = $this->container->get($name);

        if ($proxyUsage !== null) {
            $proxyUsage($result);
        }
        return $isLoaded;
    }
}
