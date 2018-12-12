<?php

namespace PHPassword\Serializer;


use PHPassword\Locator\Factory\FactoryInterface;
use PHPassword\Locator\SetLocatorImplementation;
use PHPassword\Serializer\Strategy\ObjectSerializerStrategy;

class SerializerFactory implements FactoryInterface
{
    use SetLocatorImplementation;

    /**
     * @return Serializer
     */
    public function createSerializer(): Serializer
    {
        $serializer = new Serializer();
        $serializer->addStrategy(new ObjectSerializerStrategy());

        return $serializer;
    }
}