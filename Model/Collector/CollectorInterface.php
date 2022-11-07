<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\Collector;

interface CollectorInterface
{
    const STATUS_SUCCESS = 'green';
    const STATUS_ERROR   = 'red';
    const STATUS_WARNING = 'yellow';
    const STATUS_DEFAULT = 'normal';

    public function collect(): CollectorInterface;

    public function isEnabled(): bool;

    public function getData(): array;

    public function setData(array $data): CollectorInterface;

    public function getName(): string;

    public function getStatus(): string;
}
