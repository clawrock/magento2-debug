<?php
declare(strict_types=1);

namespace ClawRock\Debug\Helper;

class Formatter
{
    private \ClawRock\Debug\Helper\Config $config;

    public function __construct(
        \ClawRock\Debug\Helper\Config $config
    ) {
        $this->config = $config;
    }

    public function microtime(float $value, ?int $precision = null): string
    {
        if ($precision === null) {
            $precision = $this->config->getTimePrecision();
        }

        return sprintf('%0.' . $precision . 'f', $value * 1000);
    }

    public function revertMicrotime(string $value): float
    {
        return (float) $value / 1000;
    }

    public function toMegaBytes(int $value, int $precision = 0): string
    {
        return sprintf('%0.' . $precision . 'f', $value / 1024 /1024);
    }

    public function percentage(float $value, int $precision = 5): string
    {
        return sprintf('%.' . $precision . 'f%%', $value * 100);
    }
}
