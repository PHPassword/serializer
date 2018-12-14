<?php

namespace PHPassword\Serializer;


class ObjectNormalizer implements NormalizerInterface
{
    /**
     * @param mixed $data
     * @return bool
     */
    public function supportsData($data)
    {
        return is_object($data);
    }

    /**
     * @param object $object
     * @return array
     * @throws SerializationException
     * @throws \ReflectionException
     */
    public function normalize($object)
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