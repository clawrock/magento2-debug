<?php

namespace ClawRock\Debug\Helper;

class Formatter
{
    /**
     * @var \ClawRock\Debug\Helper\Config
     */
    private $config;

    public function __construct(
        \ClawRock\Debug\Helper\Config $config
    ) {
        $this->config = $config;
    }

    public function microtime(float $value, int $precision = null)
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

    public function toMegaBytes(int $value, int $precision = 0)
    {
        return sprintf('%0.' . $precision . 'f', $value / 1024 /1024);
    }

    public function percentage(float $value, int $precision = 5)
    {
        return sprintf('%.' . $precision . 'f%%', $value * 100);
    }
}
