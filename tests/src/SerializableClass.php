<?php

namespace PHPassword\UnitTest;

class SerializableClass
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var SerializableClass|null
     */
    private $serializableClass;

    /**
     * @var int
     */
    private $hiddenSecret;

    /**
     * SerializableClass constructor.
     * @param int $id
     * @param string $name
     * @param SerializableClass|null $serializableClass
     */
    public function __construct(int $id, string $name, ?SerializableClass $serializableClass = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->serializableClass = $serializableClass;
        $this->hiddenSecret = rand(0, 9999);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return SerializableClass|null
     */
    public function getSerializableClass(): ?SerializableClass
    {
        return $this->serializableClass;
    }
}