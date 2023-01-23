<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\Collector;

use ClawRock\Debug\Logger\LoggableInterface;
use ClawRock\Debug\Model\Info\CacheInfo;

class CacheCollector implements CollectorInterface, LoggerCollectorInterface
{
    public const NAME = 'cache';

    public const BACKEND_NAME    = 'backend_name';
    public const BACKEND_OPTIONS = 'backend_options';
    public const CACHE_LIST      = 'cache_list';
    public const INVALIDATED     = 'invalidated_types';
    public const CACHE_LOG       = 'cache_log';
    public const STATS           = 'stats';
    public const TOTAL_TIME      = 'total_time';
    public const CACHE_STATUS    = 'status';

    private \ClawRock\Debug\Helper\Config $config;
    private \ClawRock\Debug\Model\DataCollector $dataCollector;
    private \ClawRock\Debug\Logger\DataLogger $dataLogger;
    private \ClawRock\Debug\Model\Info\CacheInfo $cacheInfo;
    private \ClawRock\Debug\Helper\Formatter $formatter;

    public function __construct(
        \ClawRock\Debug\Helper\Config $config,
        \ClawRock\Debug\Model\DataCollectorFactory $dataCollectorFactory,
        \ClawRock\Debug\Logger\DataLoggerFactory $dataLogger,
        \ClawRock\Debug\Model\Info\CacheInfo $cacheInfo,
        \ClawRock\Debug\Helper\Formatter $formatter
    ) {
        $this->config = $config;
        $this->dataCollector = $dataCollectorFactory->create();
        $this->dataLogger = $dataLogger->create();
        $this->cacheInfo = $cacheInfo;
        $this->formatter = $formatter;
    }

    public function collect(): CollectorInterface
    {
        $this->dataCollector->setData([
            self::BACKEND_NAME => $this->cacheInfo->getBackendClass(),
            self::BACKEND_OPTIONS => $this->cacheInfo->getBackendOptions(),
            self::INVALIDATED => $this->cacheInfo->getInvalidated(),
            self::CACHE_LOG => $this->dataLogger->getLogs(),
            self::STATS => $this->cacheInfo->getStats($this->dataLogger->getLogs()),
            self::TOTAL_TIME => $this->cacheInfo->getTotalTime($this->dataLogger->getLogs()),
            self::CACHE_LIST => $this->cacheInfo->getCacheList(),
        ]);

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->config->isCacheCollectorEnabled();
    }

    public function getBackendName(): string
    {
        return $this->dataCollector->getData(self::BACKEND_NAME) ?? 'Unknown';
    }

    public function getBackendOptions(): array
    {
        return $this->dataCollector->getData(self::BACKEND_OPTIONS) ?? [];
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getStats(string $key = '')
    {
        if ($key) {
            return $this->dataCollector->getData(self::STATS)[$key] ?? null;
        }

        return $this->dataCollector->getData(self::STATS) ?? [];
    }

    /**
     * @return mixed
     */
    public function getCacheCalls()
    {
        return $this->getStats(CacheInfo::STATS_TOTAL);
    }

    public function getTotalTime(): string
    {
        return $this->formatter->microtime($this->dataCollector->getData(self::TOTAL_TIME) ?? 0);
    }

    public function getCacheList(): array
    {
        return $this->dataCollector->getData(self::CACHE_LIST) ?? [];
    }

    public function getCacheCurrentStatus(string $id): bool
    {
        $cache = $this->cacheInfo->getCacheList()[$id] ?? null;

        try {
            return (bool) $cache->getStatus();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getCacheLog(): array
    {
        return $this->dataCollector->getData(self::CACHE_LOG) ?? [];
    }

    public function isCacheTypeEnabled(string $type): bool
    {
        return $this->dataCollector->getData(self::CACHE_LIST)[$type][self::CACHE_STATUS] ?? false;
    }

    public function isInvalidated(string $type): bool
    {
        return in_array($type, $this->dataCollector->getData(self::INVALIDATED));
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

    public function log(LoggableInterface $value): LoggerCollectorInterface
    {
        $this->dataLogger->log($value);

        return $this;
    }
}
