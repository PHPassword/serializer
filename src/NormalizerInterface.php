<?php

namespace PHPassword\Serializer;

interface NormalizerInterface
{
    /**
     * @param mixed $data
     * @return bool
     */
    public function supportsData($data);

    /**
     * @param object $object
     * @return array
     * @throws SerializationException
     * @throws \ReflectionException
     */
    public function normalize($object);
}