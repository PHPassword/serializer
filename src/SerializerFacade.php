<?php

namespace PHPassword\Serializer;


use PHPassword\Locator\Facade\FacadeInterface;
use PHPassword\Locator\SetLocatorImplementation;

class SerializerFacade implements FacadeInterface
{
    use SetLocatorImplementation;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @param mixed $data
     * @return string
     * @throws SerializationException
     * @throws \ReflectionException
     */
    public function serialize($data): string
    {
        return $this->getSerializer()->serialize($data);
    }

    /**
     * @param string $data
     * @param string $class
     * @return object
     * @throws SerializationException
     */
    public function deserialize(string $data, string $class)
    {
        return $this->getSerializer()->deserialize($data, $class);
    }

    /**
     * @return Serializer
     */
    private function getSerializer(): Serializer
    {
        if($this->serializer === null){
            $this->serializer = $this->locator->serializer()->factory()->createSerializer();
        }

        return $this->serializer;
    }
}