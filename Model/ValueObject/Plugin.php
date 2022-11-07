<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\ValueObject;

class Plugin
{
    private string $class;
    private string $name;
    private int $sortOrder;
    private string $method;
    private string $type;

    public function __construct(string $class, string $name, int $sortOrder, string $method, string $type)
    {
        $this->class = $class;
        $this->name = $name;
        $this->sortOrder = $sortOrder;
        $this->method = $method;
        $this->type = $type;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
