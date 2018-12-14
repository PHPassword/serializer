<?php

use PHPassword\Serializer\ObjectNormalizer;
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
        $this->serializer = new Serializer([new ObjectNormalizer()]);
    }

    /**
     * @throws \Exception
     */
    public function testSerialize()
    {
        $serialized = $this->serializer->serialize(
            new SerializableClass(1, 'Son-Goku', new SerializableClass(0, 'Bardock'))
        );

        $this->assertJson($serialized);
        $decoded = json_decode($serialized, true);
        $this->assertArrayHasKey('id', $decoded);
        $this->assertSame(1, $decoded['id']);
        $this->assertArrayHasKey('name', $decoded);
        $this->assertSame('Son-Goku', $decoded['name']);
        $this->assertArrayHasKey('serializableClass', $decoded);
        $this->assertArrayNotHasKey('hiddenSecret', $decoded);

        $nestedData = $decoded['serializableClass'];
        $this->assertArrayHasKey('id', $nestedData);
        $this->assertSame(0, $nestedData['id']);
        $this->assertArrayHasKey('name', $nestedData);
        $this->assertSame('Bardock', $nestedData['name']);
        $this->assertArrayHasKey('serializableClass', $nestedData);
        $this->assertNull($nestedData['serializableClass']);
        $this->assertArrayNotHasKey('hiddenSecret', $nestedData);
    }

    /**
     * @throws \Exception
     */
    public function testDeserialize()
    {
        $serializable = new SerializableClass(10, 'Son-Gohan', new SerializableClass(11, 'Piccolo'));
        $serialized = $this->serializer->serialize($serializable);

        /* Remove read only vars for deserializing */
        $decoded = json_decode($serialized, true);
        unset($decoded['readOnlyVar'], $decoded['serializableClass']['readOnlyVar']);
        $serialized = json_encode($decoded);
        /* End */

        /* @var SerializableClass $deserialized */
        $deserialized = $this->serializer->deserialize($serialized, SerializableClass::class);

        $this->assertInstanceOf(SerializableClass::class, $deserialized);
        $this->assertSame($serializable->getName(), $deserialized->getName());
        $this->assertSame($serializable->getSerializableClass()->getId(), $serializable->getSerializableClass()->getId());
    }

    /**
     * @throws SerializationException
     */
    public function testDeserializeFailsInvalidJson()
    {
        $this->expectException(SerializationException::class);
        $this->serializer->deserialize('{"incomplete": "json", ', SerializableClass::class);
    }

    /**
     * @throws SerializationException
     */
    public function testDeserializeFailsNonExistentClass()
    {
        $this->expectException(SerializationException::class);
        $this->serializer->deserialize('{"id": 5}', 'Class\\That\\Does\\Not\\Exist');
    }

    /**
     * @throws SerializationException
     */
    public function testDeserializeFailsNoNormalizer()
    {
        $serializer = new Serializer();
        $this->expectException(SerializationException::class);
        $serializer->deserialize('{"name":"Shenlong"}', SerializableClass::class);
    }
}