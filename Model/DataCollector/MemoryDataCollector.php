<?php

namespace ClawRock\Debug\Model\DataCollector;

class MemoryDataCollector extends AbstractDataCollector implements LateDataCollectorInterface
{
    const NAME = 'memory';

    const MEMORY_USAGE = 'memory_usage';
    const MEMORY_LIMIT = 'memory_limit';

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param \Magento\Framework\App\Request\Http  $request
     * @param \Magento\Framework\App\Response\Http $response
     * @return $this
     */
    public function collect(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\Response\Http $response
    ) {
        $this->data = [
            self::MEMORY_USAGE => 0,
            self::MEMORY_LIMIT => $this->convertToBytes($this->getCurrentMemoryLimit()),
        ];

        $this->updateMemoryUsage();

        return $this;
    }

    public function lateCollect()
    {
        $this->updateMemoryUsage();
    }

    public function getMemoryUsage()
    {
        return $this->data[self::MEMORY_USAGE];
    }

    public function getMemoryLimit()
    {
        return $this->data[self::MEMORY_LIMIT];
    }

    public function updateMemoryUsage()
    {
        $this->data[self::MEMORY_USAGE] = $this->getCurrentPeakMemoryUsage();
    }

    protected function getCurrentMemoryLimit()
    {
        return ini_get('memory_limit');
    }

    protected function getCurrentPeakMemoryUsage()
    {
        return memory_get_peak_usage(true);
    }

    private function convertToBytes($memoryLimit)
    {
        if ('-1' === $memoryLimit) {
            return -1;
        }

        $memoryLimit = strtolower($memoryLimit);
        $max = strtolower(ltrim($memoryLimit, '+'));
        if (0 === strpos($max, '0x')) {
            $max = intval($max, 16);
        } elseif (0 === strpos($max, '0')) {
            $max = intval($max, 8);
        } else {
            $max = (int) $max;
        }

        switch (substr($memoryLimit, -1)) {
            case 't':
                $max *= 1024;
            case 'g':
                $max *= 1024;
            case 'm':
                $max *= 1024;
            case 'k':
                $max *= 1024;
        }

        return $max;
    }

    public function isEnabled()
    {
        return $this->helper->isMemoryDataCollectorEnabled();
    }
}
