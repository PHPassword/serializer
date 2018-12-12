<?php

use PHPassword\Serializer\Serializer;
use PHPassword\Serializer\SerializerFactory;
use PHPUnit\Framework\TestCase;

class SerializerFactoryTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testCreateSerializer()
    {
        $factory = new SerializerFactory();
        $this->assertInstanceOf(Serializer::class, $factory->createSerializer());
    }
}