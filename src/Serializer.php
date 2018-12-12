<?php

namespace PHPassword\Serializer;


use PHPassword\Serializer\Strategy\SerializerStrategyInterface;

class Serializer
{
    /**
     * @var SerializerStrategyInterface[]
     */
    private $strategies = [];

    /**
     * @param SerializerStrategyInterface $strategy
     */
    public function addStrategy(SerializerStrategyInterface $strategy)
    {
        $this->strategies[get_class($strategy)] = $strategy;
    }

    /**
     * @param mixed $data
     * @return string
     * @throws SerializationException
     */
    public function serialize($data)
    {
        foreach($this->strategies as $strategy){
            if($strategy->canSerializeData($data)){
                return get_class($strategy) . ':::' . $strategy->serialize($data);
            }
        }

        throw new SerializationException('No strategy for serializing data');
    }

    /**
     * @param string $data
     * @return object
     * @throws SerializationException
     */
    public function deserialize(string $data)
    {
        if(($pos = strpos($data, ':::')) === false
            || !class_exists(($strategyClass = substr($data, 0, $pos)))
            || !isset($this->strategies[$strategyClass])){
            throw new SerializationException(sprintf('Invalid string "%s"', $data));
        }

        $strategy = $this->strategies[$strategyClass];
        $data = substr($data, $pos + 3);
        return $strategy->deserialize($data);
    }
}