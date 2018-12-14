<?php

namespace PHPassword\Serializer;


use PHPassword\Locator\Factory\FactoryInterface;
use PHPassword\Locator\SetLocatorImplementation;

class SerializerFactory implements FactoryInterface
{
    use SetLocatorImplementation;

    /**
     * @return Serializer
     */
    public function createSerializer(): Serializer
    {
        return new Serializer([new ObjectNormalizer()]);
    }
}