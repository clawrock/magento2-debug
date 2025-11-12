<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\ValueObject;

use ClawRock\Debug\Logger\LoggableInterface;

class ModelAction implements LoggableInterface
{
    public const LOAD = 'load';
    public const SAVE = 'save';
    public const DELETE = 'delete';
    public const LOOP_LOAD = 'loop_load';

    private string $id;

    public function __construct(
        private string $name,
        private string $model,
        private float $time,
        private array $trace = []
    ) {
        $this->id = uniqid();
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
