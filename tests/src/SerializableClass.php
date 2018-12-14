<?php

namespace PHPassword\UnitTest;

class SerializableClass
{
    /**
     * @var int
     */
    private static $foo = 1;

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
     * @return int
     */
    public static function getFoo(): int
    {
        return self::$foo;
    }

    /**
     * @param int $foo
     */
    public static function setFoo(int $foo): void
    {
        self::$foo = $foo;
    }

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

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param SerializableClass|null $serializableClass
     */
    public function setSerializableClass(?SerializableClass $serializableClass): void
    {
        $this->serializableClass = $serializableClass;
    }
}