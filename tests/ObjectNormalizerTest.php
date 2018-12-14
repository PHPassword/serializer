<?php


use PHPassword\Serializer\ObjectNormalizer;
use PHPassword\Serializer\SerializationException;
use PHPassword\UnitTest\SerializableClass;
use PHPassword\UnitTest\SerializableClassWithoutContrcutor;
use PHPUnit\Framework\TestCase;

class ObjectNormalizerTest extends TestCase
{
    /**
     * @var ObjectNormalizer
     */
    private $normalizer;

    public function setUp()
    {
        $this->normalizer = new ObjectNormalizer();
    }

    /**
     * @throws Exception
     */
    public function testSupportsNormalization()
    {
        $this->assertTrue($this->normalizer->supportsNormalization(new \stdClass()));
        $this->assertFalse($this->normalizer->supportsNormalization(5));
    }

    /**
     * @throws Exception
     */
    public function testSupportsDenormalization()
    {
        $this->assertTrue($this->normalizer->supportDenormalization([], SerializableClass::class));
        $this->assertTrue($this->normalizer->supportDenormalization([], 'NonExistentClass'));
    }

    /**
     * @throws ReflectionException
     * @throws \Exception
     */
    public function testNormalize()
    {
        $object = new SerializableClass(1000, 'Nail', new SerializableClass(2000, 'Dende'));
        $normalized = $this->normalizer->normalize($object);

        $this->assertArrayHasKey('id', $normalized);
        $this->assertSame($normalized['id'], $object->getId());
        $this->assertArrayHasKey('name', $normalized);
        $this->assertSame($normalized['name'], $object->getName());
        $this->assertArrayHasKey('serializableClass', $normalized);
        $this->assertArrayHasKey('readOnlyVar', $normalized);
        $this->assertArrayNotHasKey('foo', $normalized);
        $this->assertSame($normalized['serializableClass']['id'], $object->getSerializableClass()->getId());
        $this->assertSame($normalized['serializableClass']['name'], $object->getSerializableClass()->getName());
        $this->assertNull($object->getSerializableClass()->getSerializableClass());
    }

    /**
     * @throws ReflectionException
     * @throws \PHPassword\Serializer\SerializationException
     */
    public function testNormalizeFails()
    {
        $this->expectException(SerializationException::class);
        $this->normalizer->normalize(/** @scrutinizer ignore-type */ 'JustAString');
    }

    /**
     * @throws \Exception
     */
    public function testDenormalize()
    {
        $data = [
            'id' => 4444,
            'name' => 'Beerus',
            'serializableClass' => [
                'id' => 5555,
                'name' => 'Whis'
            ]
        ];

        /* @var SerializableClass $denormalized */
        $denormalized = $this->normalizer->denormalize($data, SerializableClass::class);

        $this->assertInstanceOf(SerializableClass::class, $denormalized);
        $this->assertSame($data['id'], $denormalized->getId());
        $this->assertSame($data['name'], $denormalized->getName());
        $this->assertSame($data['serializableClass']['id'], $denormalized->getSerializableClass()->getId());
        $this->assertSame($data['serializableClass']['name'], $denormalized->getSerializableClass()->getName());
        $this->assertNull($denormalized->getSerializableClass()->getSerializableClass());
    }

    /**
     * @throws \Exception
     */
    public function testDenormalizationFailInvalidAttribute()
    {
        $data = [
            'id' => 4,
            'name' => '???',
            'serializableClass' => [
                'id' => 5,
                'name' => '?????',
                'nonExistentAttribute' => true
            ]
        ];

        $this->expectException(SerializationException::class);
        $this->normalizer->denormalize($data, SerializableClass::class);
    }

    /**
     * @throws ReflectionException
     * @throws SerializationException
     */
    public function testDenormalizeFailsOnMissingSetter()
    {
        $data = [
            'id' => 4444,
            'name' => 'Beerus',
            'serializableClass' => [
                'id' => 5555,
                'name' => 'Whis',
                'readOnlyVar' => 5
            ]
        ];

        $this->expectException(SerializationException::class);
        $this->normalizer->denormalize($data, SerializableClass::class);
    }

    /**
     * @throws ReflectionException
     * @throws SerializationException
     */
    public function testDenormalizeFailsOnMissingClass()
    {
        $this->expectException(SerializationException::class);
        $this->normalizer->denormalize([], 'SomeNonExistentClass');
    }

    /**
     * @throws \Exception
     */
    public function testDenormalizeOnClassWithoutConstructor()
    {
        $data = [
            'id' => 999999,
            'name' => 'Zeno'
        ];

        $object = $this->normalizer->denormalize($data, SerializableClassWithoutContrcutor::class);
        $this->assertInstanceOf(SerializableClassWithoutContrcutor::class, $object);
        $this->assertSame($data['id'], $object->getId());
        $this->assertSame($data['name'], $object->getName());
    }
}