<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\Info;

class MemoryInfo
{
    public function getCurrentMemoryLimit(): int
    {
        return $this->convertToBytes((string) ini_get('memory_limit'));
    }

    public function getCurrentPeakMemoryUsage(): int
    {
        return memory_get_peak_usage(true);
    }

    private function convertToBytes(string $memoryLimit): int
    {
        if ('-1' === $memoryLimit) {
            return -1;
        }

        $memoryLimit = strtolower($memoryLimit);
        $max = $this->readValue($memoryLimit);

        switch (substr($memoryLimit, -1)) {
            case 't':
                $max *= 1024;
                // no break
            case 'g':
                $max *= 1024;
                // no break
            case 'm':
                $max *= 1024;
                // no break
            case 'k':
                $max *= 1024;
        }

        return $max;
    }

    private function readValue(string $memoryLimit): int
    {
        $value = ltrim($memoryLimit, '+');
        if (0 === strpos($value, '0x')) {
            // phpcs:ignore Magento2.Functions.DiscouragedFunction.DiscouragedWithAlternative
            return intval($value, 16);
        }

        if (0 === strpos($value, '0')) {
            // phpcs:ignore Magento2.Functions.DiscouragedFunction.DiscouragedWithAlternative
            return intval($value, 8);
        }

        return (int) $value;
    }
}
