<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\ValueObject;

use ClawRock\Debug\Logger\LoggableInterface;

class EventObserver implements LoggableInterface
{
    private string $id;

    public function __construct(
        private string $name,
        private string $class,
        private string $event,
        private float $time
    ) {
        $this->id = uniqid();
    }

    public function getId(): string
    {
        return $this->getName() . '_' . $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getEvent(): string
    {
        return $this->event;
    }

    public function getTime(): float
    {
        return $this->time;
    }
}
