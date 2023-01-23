<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\Collector;

interface CollectorInterface
{
    public const STATUS_SUCCESS = 'green';
    public const STATUS_ERROR = 'red';
    public const STATUS_WARNING = 'yellow';
    public const STATUS_DEFAULT = 'normal';

    public function collect(): CollectorInterface;

    public function isEnabled(): bool;

    public function getData(): array;

    public function setData(array $data): CollectorInterface;

    public function getName(): string;

    public function getStatus(): string;
}
