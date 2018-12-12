<?php


use PHPassword\Serializer\SerializationException;
use PHPassword\Serializer\Strategy\ObjectSerializerStrategy;
use PHPassword\UnitTest\SerializableClass;
use PHPUnit\Framework\TestCase;

class ObjectSerializerStrategyTest extends TestCase
{
    /**
     * @var ObjectSerializerStrategy
     */
    private $strategy;

    public function setUp()
    {
        $this->strategy = new ObjectSerializerStrategy();
    }

    /**
     * @throws Exception
     */
    public function testCanSerialize()
    {
        $this->assertTrue($this->strategy->canSerializeData(new \stdClass()));
        $this->assertFalse($this->strategy->canSerializeData('foo'));
    }

    /**
     * @throws Exception
     */
    public function testSerialize()
    {
        $firstSerializable = new SerializableClass(9000, 'Vegeta');
        $secondSerializable = new SerializableClass(1337, 'Trunks', $firstSerializable);
        $firstSerialized = $this->strategy->serialize($firstSerializable);
        $secondSerialized = $this->strategy->serialize($secondSerializable);

        $this->assertJson($firstSerialized);
        $this->assertJson($secondSerialized);
        $this->assertNotFalse(strpos($firstSerialized, '\\"name\\":\\"Vegeta\\"'));
        $this->assertFalse(strpos($firstSerialized, '\\hiddenSecret\\:'));
        $this->assertNotFalse(strpos($secondSerialized, '\\"name\\":\\"Vegeta\\"'));
        $this->assertNotFalse(strpos($secondSerialized, '\\"id\\":1337'));
        $this->assertFalse(strpos($secondSerialized, '\\hiddenSecret\\:'));
    }

    /**
     * @throws SerializationException
     */
    public function testSerializationFails()
    {
        $this->expectException(SerializationException::class);
        $this->strategy->serialize('String');
    }

    /**
     * @throws Exception
     */
    public function testDeserialize()
    {
        $testString = file_get_contents(__DIR__ . '/src/serialized.json');
        /* @var SerializableClass $deserialized */
        $deserialized = $this->strategy->deserialize($testString);

        $this->assertInstanceOf(SerializableClass::class, $deserialized);
        $this->assertSame('Trunks', $deserialized->getName());
        $this->assertSame('Vegeta', $deserialized->getSerializableClass()->getName());
    }
}