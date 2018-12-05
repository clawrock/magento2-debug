<?php

namespace ClawRock\Debug\Model\DataCollector;

class DatabaseDataCollector extends AbstractDataCollector
{
    const NAME = 'database';

    const TOTAL_TIME  = 'total_time';
    const QUERY_COUNT = 'query_count';
    const QUERIES     = 'queries';
    const PROFILE     = 'profile';

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resourceConnection;

    public function __construct(
        \ClawRock\Debug\Helper\Profiler $helper,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        parent::__construct($helper);

        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param \Magento\Framework\HTTP\PhpEnvironment\Request  $request
     * @param \Magento\Framework\HTTP\PhpEnvironment\Response $response
     * @return $this
     */
    public function collect(
        \Magento\Framework\HTTP\PhpEnvironment\Request $request,
        \Magento\Framework\HTTP\PhpEnvironment\Response $response
    ) {
        /** @var \Magento\Framework\DB\Adapter\Pdo\Mysql $connection */
        $connection = $this->resourceConnection->getConnection();
        $profiler = $connection->getProfiler();

        $queries = [];

        foreach ($profiler->getQueryProfiles() as $queryProfile) {
            $queries[] = [self::PROFILE => $queryProfile];
        }

        $this->data = [
            self::TOTAL_TIME  => $profiler->getTotalElapsedSecs() * 1000,
            self::QUERY_COUNT => $profiler->getTotalNumQueries(),
            self::QUERIES     => $queries,
        ];

        return $this;
    }

    public function getQueries()
    {
        return $this->data[self::QUERIES] ?? [];
    }

    public function getTotalTime()
    {
        return $this->data[self::TOTAL_TIME] ?? 'n/a';
    }

    public function getQueryCount()
    {
        return $this->data[self::QUERY_COUNT] ?? 'n/a';
    }

    public function isEnabled()
    {
        return $this->helper->isDatabaseDataCollectorEnabled()
            && $this->resourceConnection->getConnection()->getProfiler()->getQueryProfiles() !== false;
    }
}
