<?php

namespace ClawRock\Debug\Model\ValueObject;

use ClawRock\Debug\Logger\LoggableInterface;

class EventObserver implements LoggableInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $event;

    /**
     * @var float
     */
    private $time;

    public function __construct(
        string $name,
        string $class,
        string $event,
        float $time
    ) {
        $this->id = uniqid();
        $this->name = $name;
        $this->class = $class;
        $this->event = $event;
        $this->time = $time;
    }

    public function getId()
    {
        return $this->getName() . '_' . $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
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
    public function getEvent(): string
    {
        return $this->event;
    }

    /**
     * @return float
     */
    public function getTime(): float
    {
        return $this->time;
    }
}
