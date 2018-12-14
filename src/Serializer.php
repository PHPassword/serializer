<?php

namespace PHPassword\Serializer;

class Serializer
{
    /**
     * @var NormalizerInterface[]
     */
    private $normalizerCollection = [];

    /**
     * ObjectSerializer constructor.
     * @param NormalizerInterface[] $normalizerCollection
     */
    public function __construct(array $normalizerCollection = [])
    {
        foreach($normalizerCollection as $normalizer){
            $this->addNormalizer($normalizer);
        }
    }

    /**
     * @param NormalizerInterface $normalizer
     */
    public function addNormalizer(NormalizerInterface $normalizer): void
    {
        $this->normalizerCollection[] = $normalizer;
    }

    /**
     * @param mixed $data
     * @return false|string
     * @throws SerializationException
     * @throws \ReflectionException
     */
    public function serialize($data)
    {
        foreach($this->normalizerCollection as $normalizer){
            if($normalizer->supportsNormalization($data)){
                $data = $normalizer->normalize($data);
                break;
            }
        }

        return json_encode($data);
    }

    /**
     * @param string $data
     * @param string $class
     * @return object
     * @throws SerializationException
     */
    public function deserialize(string $data, string $class)
    {
        if(!class_exists($class)){
            throw new SerializationException('Cannot find target class');
        }

        $data = json_decode($data, true);
        if(json_last_error() !== JSON_ERROR_NONE){
            throw new SerializationException(sprintf('JSON error %s', json_last_error_msg()));
        }

        foreach($this->normalizerCollection as $normalizer){
            if($normalizer->supportDenormalization($data, $class)){
                return $normalizer->denormalize($data, $class);
            }
        }

        throw new SerializationException('You must register a normalizer that can handle the denormalization');
    }
}