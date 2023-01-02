<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\ValueObject;

class LoopModelAction
{
    private \ClawRock\Debug\Model\ValueObject\ModelAction $modelAction;
    private float $time;
    private int $count;

    public function __construct(ModelAction $modelAction, float $time, int $count)
    {
        $this->modelAction = $modelAction;
        $this->time = $time;
        $this->count = $count;
    }

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

    public function getTime(): float
    {
        return $this->time;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
