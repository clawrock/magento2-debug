<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\ValueObject;

use ClawRock\Debug\Logger\LoggableInterface;

class ModelAction implements LoggableInterface
{
    const LOAD   = 'load';
    const SAVE   = 'save';
    const DELETE = 'delete';

    const LOOP_LOAD = 'loop_load';

    private string $id;
    private string $name;
    private string $model;
    private float $time;
    private array $trace;

    public function __construct(string $name, string $model, float $time, array $trace = [])
    {
        $this->id = uniqid();
        $this->name = $name;
        $this->model = $model;
        $this->time = $time;
        $this->trace = $trace;
    }

    public function getId(): string
    {
        return $this->name . '::' . $this->id . '::' . $this->model;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getTime(): float
    {
        return $this->time;
    }

    public function getTrace(): array
    {
        return $this->trace;
    }

    public function getTraceHash(): string
    {
        if (empty($this->getTrace())) {
            return '';
        }

        // phpcs:ignore Magento2.Security.InsecureFunction.FoundWithAlternative
        return md5(serialize($this->getTrace()));
    }
}
