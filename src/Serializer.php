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
            if($normalizer->supportsData($data)){
                $data = $normalizer->normalize($data);
                break;
            }
        }

        return json_encode($data);
    }
}