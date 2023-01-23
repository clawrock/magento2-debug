<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\Collector;

class MemoryCollector implements CollectorInterface, LateCollectorInterface
{
    public const NAME = 'memory';
    public const MEMORY_USAGE = 'memory_usage';
    public const MEMORY_LIMIT = 'memory_limit';

    private \ClawRock\Debug\Helper\Config $config;
    private \ClawRock\Debug\Model\DataCollector $dataCollector;
    private \ClawRock\Debug\Model\Info\MemoryInfo $memoryInfo;
    private \ClawRock\Debug\Helper\Formatter $formatter;

    public function __construct(
        \ClawRock\Debug\Helper\Config $config,
        \ClawRock\Debug\Model\DataCollectorFactory $dataCollectorFactory,
        \ClawRock\Debug\Model\Info\MemoryInfo $memoryInfo,
        \ClawRock\Debug\Helper\Formatter $formatter
    ) {
        $this->config = $config;
        $this->dataCollector = $dataCollectorFactory->create();
        $this->memoryInfo = $memoryInfo;
        $this->formatter = $formatter;
    }

    public function collect(): CollectorInterface
    {
        $this->dataCollector->setData([
            self::MEMORY_USAGE => $this->memoryInfo->getCurrentPeakMemoryUsage(),
            self::MEMORY_LIMIT => $this->memoryInfo->getCurrentMemoryLimit(),
        ]);

        return $this;
    }

    public function lateCollect(): LateCollectorInterface
    {
        $this->dataCollector->addData(self::MEMORY_USAGE, $this->memoryInfo->getCurrentPeakMemoryUsage());

        return $this;
    }

    public function getMemoryUsage(): string
    {
        return $this->formatter->toMegaBytes($this->dataCollector->getData(self::MEMORY_USAGE), 1);
    }

    public function getMemoryLimit(): string
    {
        return $this->formatter->toMegaBytes($this->dataCollector->getData(self::MEMORY_LIMIT));
    }

    public function hasMemoryLimit(): bool
    {
        return $this->dataCollector->getData(self::MEMORY_LIMIT) !== -1;
    }

    public function isEnabled(): bool
    {
        return $this->config->isMemoryCollectorEnabled();
    }

    public function getData(): array
    {
        return $this->dataCollector->getData();
    }

    public function setData(array $data): CollectorInterface
    {
        $this->dataCollector->setData($data);

        return $this;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getStatus(): string
    {
        return self::STATUS_DEFAULT;
    }
}
