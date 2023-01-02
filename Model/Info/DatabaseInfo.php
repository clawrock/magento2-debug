<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\Info;

class DatabaseInfo
{
    const PROFILE    = 'profile';

    const ALL_QUERIES        = 'all';
    const DUPLICATED_QUERIES = 'duplicated';

    private \Magento\Framework\App\ResourceConnection $resourceConnection;
    private ?\Zend_Db_Profiler $profiler = null;
    private \ClawRock\Debug\Helper\Database $databaseHelper;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \ClawRock\Debug\Helper\Database $databaseHelper
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->databaseHelper = $databaseHelper;
    }

    public function getQueries(): array
    {
        $queries = [
            self::ALL_QUERIES => [],
            \Zend_Db_Profiler::SELECT => [],
            \Zend_Db_Profiler::INSERT => [],
            \Zend_Db_Profiler::UPDATE => [],
            \Zend_Db_Profiler::DELETE => [],
            \Zend_Db_Profiler::QUERY => [],
        ];

        $queryProfiles = $this->getProfiler()->getQueryProfiles();
        if ($queryProfiles === false) {
            return $queries;
        }

        /** @var \Zend_Db_Profiler_Query $query */
        foreach ($queryProfiles as $query) {
            $type = $query->getQueryType();
            if (!isset($queries[$type])) {
                $type = \Zend_Db_Profiler::QUERY;
            }
            $queries[$type][] = $query;

            $queries[self::ALL_QUERIES][] = $query;
        }

        $queries[self::DUPLICATED_QUERIES] = $this->databaseHelper->getDuplicatedQueries();

        return $queries;
    }

    public function getTotalTime(): float
    {
        return $this->getProfiler()->getTotalElapsedSecs();
    }

    public function getQueriesCount(): int
    {
        return $this->getProfiler()->getTotalNumQueries();
    }

    private function getProfiler(): \Zend_Db_Profiler
    {
        if ($this->profiler === null) {
            /** @var \Magento\Framework\DB\Adapter\Pdo\Mysql $connection */
            $connection = $this->resourceConnection->getConnection();
            $this->profiler = $connection->getProfiler();
        }

        return $this->profiler;
    }
}
