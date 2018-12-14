<?php

namespace PHPassword\Serializer;


class ObjectNormalizer implements NormalizerInterface
{
    /**
     * @param mixed $data
     * @return bool
     */
    public function supportsNormalization($data): bool
    {
        return is_object($data);
    }

    /**
     * @param array $data
     * @param string $class
     * @return bool
     */
    public function supportDenormalization(array $data, string $class): bool
    {
        return true;
    }

    /**
     * @param array $data
     * @param string $class
     * @return object
     * @throws \ReflectionException
     */
    public function denormalize(array $data, string $class)
    {
        $reflection = new \ReflectionClass($class);
        $entity = $reflection->newInstanceWithoutConstructor();

        foreach($data as $propertyName => $propertyValue){
            if(!$reflection->hasMethod('set' . ucfirst($propertyName))){
                continue;
            }

            $method = $reflection->getMethod('set' . ucfirst($propertyName));
            $this->setProperty($method, $propertyValue, $entity);
        }

        return $entity;
    }

    /**
     * @param \ReflectionMethod $method
     * @param $value
     * @param $entity
     * @throws \ReflectionException
     */
    private function setProperty(\ReflectionMethod $method, $value, $entity)
    {
        if($method->getNumberOfRequiredParameters() !== 1){
            throw new \LogicException(sprintf('Invalid setter %s', $method->getName()));
        }

        /* @var \ReflectionParameter $parameter */
        $parameter = $method->getParameters()[0];
        if(class_exists($parameter->getType()->getName())){
            $value = $this->denormalize($value, $parameter->getType()->getName());
        }

        $method->invoke($entity, $value);
    }

    /**
     * @param object $object
     * @return array
     * @throws SerializationException
     * @throws \ReflectionException
     */
    public function normalize($object): array
    {
        $attributes = [];

        if(!is_object($object)){
            throw new SerializationException('Argument is no object');
        }

        $reflection = new \ReflectionClass($object);

        foreach($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflMethod){
            if (!$this->isPossibleValidMethod($reflMethod)
                || ($expectedAttribute = $this->getExpectedAttributeName($reflMethod)) === ''
                || !$reflection->hasProperty($expectedAttribute)) {
                continue;
            }

            $value = $reflMethod->invoke($object);
            if(is_object($value)){
                $value = $this->normalize($value);
            }

            $attributes[$expectedAttribute] = $value;
        }

        return $attributes;
    }

    /**
     * @param \ReflectionMethod $reflMethod
     * @return bool
     */
    private function isPossibleValidMethod(\ReflectionMethod $reflMethod): bool
    {
        return $reflMethod->getNumberOfRequiredParameters() === 0
            && !$reflMethod->isStatic()
            && !$reflMethod->isConstructor()
            && !$reflMethod->isDestructor();
    }

    /**
     * @param \ReflectionMethod $reflMethod
     * @return string
     */
    private function getExpectedAttributeName(\ReflectionMethod $reflMethod): string
    {
        $expectedAttribute = '';
        if(in_array(substr($reflMethod->getName(), 0, 3), ['get', 'has'])){
            $expectedAttribute = lcfirst(substr($reflMethod->getName(), 3));
        }
        elseif(substr($reflMethod->getName(), 0, 2) === 'is'){
            $expectedAttribute = lcfirst(substr($reflMethod->getName(), 2));
        }

        return $expectedAttribute;
    }
}