<?php

namespace PHPassword\Serializer\Strategy;

interface SerializerStrategyInterface
{
    /**
     * @param mixed $data
     * @return bool
     */
    public function canSerializeData($data): bool;

    /**
     * @param object $data
     * @return string
     */
    public function serialize($data): string;

    /**
     * @param string $data
     * @return object
     */
    public function deserialize(string $data);
}