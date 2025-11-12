<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\ValueObject;

class Plugin
{
    public function __construct(
        private string $class,
        private string $name,
        private int $sortOrder,
        private string $method,
        private string $type
    ) {
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
