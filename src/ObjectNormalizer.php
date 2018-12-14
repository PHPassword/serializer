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
     * @throws SerializationException
     */
    public function denormalize(array $data, string $class)
    {
        try {
            $reflection = new \ReflectionClass($class);
            $entity = $reflection->newInstance();

            foreach ($data as $propertyName => $propertyValue) {
                if (!$reflection->hasProperty($propertyName)) {
                    throw new SerializationException(sprintf('Invalid property %s for class %s', $propertyName, $class));
                }

                if (!$reflection->hasMethod('set' . ucfirst($propertyName))) {
                    throw new SerializationException(sprintf('Property %s is not writable for class %s', $propertyName, $class));
                }

                $method = $reflection->getMethod('set' . ucfirst($propertyName));
                $this->setProperty($method, $propertyValue, $entity);
            }
        }
        catch(\ReflectionException $e){
            throw new SerializationException($e->getMessage(), $e->getCode(), $e);
        }

        return $entity;
    }

    /**
     * @param \ReflectionMethod $method
     * @param $value
     * @param $entity
     * @throws SerializationException
     * @throws \ReflectionException
     */
    private function setProperty(\ReflectionMethod $method, $value, $entity)
    {
        if($method->getNumberOfRequiredParameters() !== 1){
            throw new \LogicException(sprintf('Invalid setter %s', $method->getName()));
        }

        /* @var \ReflectionParameter $parameter */
        $parameter = $method->getParameters()[0];
        if($value !== null && class_exists($parameter->getType()->getName())){
            $value = $this->denormalize($value, $parameter->getType()->getName());
        }

        $method->invoke($entity, $value);
    }

    /**
     * @param object $object
     * @return array
     * @throws SerializationException
     */
    public function normalize($object): array
    {
        $attributes = [];

        if(!is_object($object)){
            throw new SerializationException('Argument is no object');
        }

        try {
            $reflection = new \ReflectionClass($object);

            foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflMethod) {
                if (!$this->isPossibleValidMethod($reflMethod)
                    || ($expectedAttribute = $this->getExpectedAttributeName($reflMethod)) === ''
                    || !$reflection->hasProperty($expectedAttribute)) {
                    continue;
                }

                $value = $reflMethod->invoke($object);
                if (is_object($value)) {
                    $value = $this->normalize($value);
                }

                $attributes[$expectedAttribute] = $value;
            }
        }
        catch(\ReflectionException $e){
            throw new SerializationException($e->getMessage(), $e->getCode(), $e);
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