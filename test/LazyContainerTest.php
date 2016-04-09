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
        $isLoaded = false;
        $this->decoratedContainer->method('get')->with('foo')->willReturnCallback(function() use (&$isLoaded) {
            $isLoaded = true;
            return new ArrayObject();
        });

        $this->container->get('foo');

        $this->assertFalse($isLoaded);
    }

    public function testInstanceIsCreatedAfterUsage()
    {
        $isLoaded = false;
        $this->decoratedContainer->method('get')->with('foo')->willReturnCallback(function() use (&$isLoaded) {
            $isLoaded = true;
            return new ArrayObject();
        });

        /* @var $object ArrayObject */
        $object = $this->container->get('foo');

        $object->ksort();

        $this->assertTrue($isLoaded);
    }

    public function testInstanceIsCreatedBeforeUsageIfNotMapped()
    {
        $isLoaded = false;
        $this->decoratedContainer->method('get')->with('bar')->willReturnCallback(function() use (&$isLoaded) {
            $isLoaded = true;
            return new ArrayObject();
        });

        $this->container->get('bar');

        $this->assertTrue($isLoaded);
    }

    public function testCheckServiceExistsWorksSameAsDecoratedContainer()
    {
        $this->decoratedContainer->method('has')->with('boo')->willReturn(true);

        $result = $this->container->has('boo');

        $this->assertTrue($result);
    }
}
