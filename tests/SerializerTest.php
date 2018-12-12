<?php

use PHPassword\Serializer\Strategy\ObjectSerializerStrategy;
use PHPassword\Serializer\SerializationException;
use PHPassword\Serializer\Serializer;
use PHPassword\UnitTest\SerializableClass;
use PHPUnit\Framework\TestCase;

class SerializerTest extends TestCase
{
    /**
     * @var Serializer
     */
    private $serializer;

    public function setUp()
    {
        $this->serializer = new Serializer();
        $this->serializer->addStrategy(new ObjectSerializerStrategy());
    }

    /**
     * @throws \Exception
     */
    public function testSerialize()
    {
        $serialized = $this->serializer->serialize(
            new SerializableClass(1, 'Son-Goku', new SerializableClass(0, 'Bardock'))
        );

        $this->assertStringStartsWith(ObjectSerializerStrategy::class . ':::', $serialized);
    }

    /**
     * @throws SerializationException
     */
    public function testSerializeFails()
    {
        $serializer = new Serializer();
        $this->expectException(SerializationException::class);
        $serializer->serialize(new SerializableClass(9, 'Radditz'));
    }

    /**
     * @throws \Exception
     */
    public function testDeserialize()
    {
        $serializable = new SerializableClass(10, 'Son-Gohan', new SerializableClass(11, 'Piccolo'));
        $serialized = $this->serializer->serialize($serializable);
        /* @var SerializableClass $deserialized */
        $deserialized = $this->serializer->deserialize($serialized);

        $this->assertInstanceOf(SerializableClass::class, $deserialized);
        $this->assertSame($serializable->getName(), $deserialized->getName());
        $this->assertSame($serializable->getSerializableClass()->getId(), $serializable->getSerializableClass()->getId());
    }

    /**
     * @throws SerializationException
     */
    public function testDeserializeFails()
    {
        $this->expectException(SerializationException::class);
        $this->serializer->deserialize('someRandomString');
    }
}