<?php

namespace PHPassword\Serializer;

interface NormalizerInterface
{
    /**
     * @param mixed $data
     * @return bool
     */
    public function supportsNormalization($data): bool;

    /**
     * @param object $object
     * @return array
     */
    public function normalize($object): array;


    /**
     * @param array $data
     * @param string $class
     * @return bool
     */
    public function supportDenormalization(array $data, string $class): bool;

    /**
     * @param array $data
     * @param string $class
     * @return object
     */
    public function denormalize(array $data, string $class);
}