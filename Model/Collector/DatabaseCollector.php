<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\Collector;

use ClawRock\Debug\Model\Info\DatabaseInfo;

class DatabaseCollector implements CollectorInterface
{
    public const NAME = 'database';
    public const TOTAL_TIME = 'total_time';
    public const QUERY_COUNT = 'query_count';
    public const QUERIES = 'queries';

    private \ClawRock\Debug\Helper\Config $config;
    private \ClawRock\Debug\Model\DataCollector $dataCollector;
    private \ClawRock\Debug\Model\Info\DatabaseInfo $databaseInfo;
    private \ClawRock\Debug\Helper\Formatter $formatter;

    public function __construct(
        \ClawRock\Debug\Helper\Config $config,
        \ClawRock\Debug\Model\DataCollectorFactory $dataCollectorFactory,
        DatabaseInfo $databaseInfo,
        \ClawRock\Debug\Helper\Formatter $formatter
    ) {
        $this->config = $config;
        $this->dataCollector = $dataCollectorFactory->create();
        $this->databaseInfo = $databaseInfo;
        $this->formatter = $formatter;
    }

    public function collect(): CollectorInterface
    {
        $this->dataCollector->setData([
            self::TOTAL_TIME  => $this->databaseInfo->getTotalTime(),
            self::QUERY_COUNT => $this->databaseInfo->getQueriesCount(),
            self::QUERIES     => $this->databaseInfo->getQueries(),
        ]);

        return $this;
    }

    public function getQueries(): array
    {
        return $this->dataCollector->getData(self::QUERIES) ?? [];
    }

    public function getTotalTime(): string
    {
        return $this->formatter->microtime($this->dataCollector->getData(self::TOTAL_TIME) ?? 0);
    }

    public function getQueriesCount(): int
    {
        return $this->dataCollector->getData(self::QUERY_COUNT) ?? 0;
    }

    public function getAllQueries(): array
    {
        return $this->dataCollector->getData(self::QUERIES)[DatabaseInfo::ALL_QUERIES] ?? [];
    }

    public function getSelectQueries(): array
    {
        return $this->dataCollector->getData(self::QUERIES)[\Zend_Db_Profiler::SELECT] ?? [];
    }

    public function getInsertQueries(): array
    {
        return $this->dataCollector->getData(self::QUERIES)[\Zend_Db_Profiler::INSERT] ?? [];
    }

    public function getUpdateQueries(): array
    {
        return $this->dataCollector->getData(self::QUERIES)[\Zend_Db_Profiler::UPDATE] ?? [];
    }

    public function getDeleteQueries(): array
    {
        return $this->dataCollector->getData(self::QUERIES)[\Zend_Db_Profiler::DELETE] ?? [];
    }

    public function getOtherQueries(): array
    {
        return $this->dataCollector->getData(self::QUERIES)[\Zend_Db_Profiler::QUERY] ?? [];
    }

    public function getDuplicatedQueries(): array
    {
        return array_column($this->dataCollector->getData(self::QUERIES)[DatabaseInfo::DUPLICATED_QUERIES], 'query');
    }

    public function isEnabled(): bool
    {
        return $this->config->isDatabaseCollectorEnabled();
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
        if (!empty($this->getDuplicatedQueries())) {
            return self::STATUS_WARNING;
        }

        return self::STATUS_DEFAULT;
    }
}
