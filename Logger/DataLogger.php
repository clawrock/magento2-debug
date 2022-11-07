<?php
declare(strict_types=1);

namespace ClawRock\Debug\Logger;

class DataLogger
{
    private array $data = [];

    public function log(LoggableInterface $value): DataLogger
    {
        $this->data[$value->getId()] = $value;

        return $this;
    }

    public function getLogs(): array
    {
        return $this->data;
    }
}
