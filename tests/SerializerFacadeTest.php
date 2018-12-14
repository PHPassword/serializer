<?php

use PHPassword\Locator\Locator;
use PHPassword\Locator\Proxy\LocatorProxyFactory;
use PHPassword\Serializer\SerializerFacade;
use PHPassword\UnitTest\SerializableClass;
use PHPUnit\Framework\TestCase;

class SerializerFacadeTest extends TestCase
{
    /**
     * @var SerializerFacade
     */
    private $facade;

    public function setUp()
    {
        $factory = new LocatorProxyFactory(new \ArrayObject(['PHPassword\\']));
        $locator = new Locator($factory);
        $this->facade = new SerializerFacade();
        $this->facade->setLocator($locator);
    }

    /**
     * @throws \Exception
     */
    public function testSerializeDeserialize()
    {
        $testObject = new SerializableClass(1, 'Nappa');
        $serialized = $this->facade->serialize($testObject);
        /* @var SerializableClass $deserialized */
        $deserialized = $this->facade->deserialize($serialized, SerializableClass::class);

        $this->assertTrue(is_string($serialized));
        $this->assertInstanceOf(SerializableClass::class, $deserialized);
        $this->assertSame($testObject->getName(), $deserialized->getName());
    }
}