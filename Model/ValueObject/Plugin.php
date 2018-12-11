<?php

namespace ClawRock\Debug\Model\ValueObject;

class Plugin
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $sortOrder;

    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $type;

    public function __construct(string $class, string $name, int $sortOrder, string $method, string $type)
    {
        $this->class = $class;
        $this->name = $name;
        $this->sortOrder = $sortOrder;
        $this->method = $method;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}
