<?php

namespace ClawRock\Debug\Model\ValueObject;

class LoopModelAction
{
    /**
     * @var \ClawRock\Debug\Model\ValueObject\ModelAction
     */
    private $modelAction;

    /**
     * @var float
     */
    private $time;

    /**
     * @var int
     */
    private $count;

    public function __construct(ModelAction $modelAction, float $time, int $count)
    {
        $this->modelAction = $modelAction;
        $this->time = $time;
        $this->count = $count;
    }

    /**
     * @return \ClawRock\Debug\Model\ValueObject\ModelAction
     */
    public function getModelAction(): ModelAction
    {
        return $this->modelAction;
    }

    public function getName(): string
    {
        return $this->modelAction->getName();
    }

    public function getModel(): string
    {
        return $this->modelAction->getModel();
    }

    public function getTrace(): array
    {
        return $this->modelAction->getTrace();
    }

    /**
     * @return float
     */
    public function getTime(): float
    {
        return $this->time;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }
}
