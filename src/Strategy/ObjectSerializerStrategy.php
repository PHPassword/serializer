<?php

namespace PHPassword\Serializer\Strategy;


use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Serializer as SymfonySerializer;

class ObjectSerializerStrategy implements SerializerStrategyInterface
{
    private const METADATA_INDEX_TYPE = '___type___';

    private const METADATA_INDEX_DATA = '___data___';

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var EncoderInterface
     */
    private $encoder;

    public function __construct()
    {
        $this->encoder = new JsonEncoder();
        $this->serializer = new SymfonySerializer([new ObjectNormalizer()], [$this->encoder]);
    }

    /**
     * @param mixed $data
     * @return bool
     */
    public function canSerializeData($data):bool
    {
        return is_object($data);
    }

    /**
     * @param object $data
     * @return string
     */
    public function serialize($data): string
    {
        if(!is_object($data)){
            throw new \LogicException('Only objects can be serialized');
        }

        return $this->encoder->encode(
            [
                self::METADATA_INDEX_TYPE => is_object($data) ? get_class($data) : gettype($data),
                self::METADATA_INDEX_DATA => $this->serializer->serialize($data, JsonEncoder::FORMAT)
            ],
            JsonEncoder::FORMAT
        );
    }

    /**
     * @param string $data
     * @return object
     */
    public function deserialize(string $data)
    {
        $data = $this->encoder->decode($data, JsonEncoder::FORMAT);
        return $this->serializer->deserialize($data[self::METADATA_INDEX_DATA], $data[self::METADATA_INDEX_TYPE], JsonEncoder::FORMAT);
    }
}