<?php

namespace ClawRock\Debug\Model\ValueObject;

use ClawRock\Debug\Logger\LoggableInterface;

class ModelAction implements LoggableInterface
{
    const LOAD   = 'load';
    const SAVE   = 'save';
    const DELETE = 'delete';

    const LOOP_LOAD = 'loop_load';

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
    private $model;

    /**
     * @var float
     */
    private $time;

    /**
     * @var array
     */
    private $trace;

    public function __construct(string $name, string $model, float $time, array $trace = [])
    {
        $this->id = uniqid();
        $this->name = $name;
        $this->model = $model;
        $this->time = $time;
        $this->trace = $trace;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->name . '::' . $this->id . '::' . $this->model;
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
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * @return float
     */
    public function getTime(): float
    {
        return $this->time;
    }

    /**
     * @return array
     */
    public function getTrace(): array
    {
        return $this->trace;
    }

    /**
     * @return string
     */
    public function getTraceHash(): string
    {
        if (empty($this->getTrace())) {
            return '';
        }

        return md5(serialize($this->getTrace()));
    }
}
