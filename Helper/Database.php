<?php
declare(strict_types=1);

namespace ClawRock\Debug\Helper;

use Magento\Framework\App\ResourceConnection;

class Database
{
    private \Magento\Framework\App\ResourceConnection $resourceConnection;
    private array $formatterCache = [];

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    public function getQueryId(\Zend_Db_Profiler_Query $query): string
    {
        // phpcs:ignore Magento2.Security.InsecureFunction.FoundWithAlternative
        return md5($query->getQuery() . implode(',', $query->getQueryParams()));
    }

    public function getDuplicatedQueries(): array
    {
        /** @var \Magento\Framework\DB\Adapter\Pdo\Mysql $connection */
        $connection = $this->resourceConnection->getConnection();
        $queryProfiles = $connection->getProfiler()->getQueryProfiles();
        if ($queryProfiles === false) {
            return [];
        }

        $queries = [];
        /** @var \Zend_Db_Profiler_Query $query */
        foreach ($queryProfiles as $query) {
            $queryId = $this->getQueryId($query);
            if (!isset($queries[$queryId])) {
                $queries[$queryId] = [
                    'count'      => 0,
                    'total_time' => 0,
                    'query'      => $query,
                ];
            }
            $queries[$queryId]['count']++;
            $queries[$queryId]['total_time'] += $query->getElapsedSecs();
        }

        $queries = array_filter($queries, function ($item) {
            return $item['count'] > 1;
        });

        usort($queries, function ($a, $b) {
            return $a['count'] - $b['count'];
        });

        return $queries;
    }

    public function formatQuery(string $sql): ?string
    {
        $cacheKey = md5('f' . $sql); // phpcs:ignore Magento2.Security.InsecureFunction.FoundWithAlternative
        if (isset($this->formatterCache[$cacheKey])) {
            return $this->formatterCache[$cacheKey];
        }

        return $this->formatterCache[$cacheKey] = preg_replace(
            '/<pre class="(.*)">([^"]*+)<\/pre>/Us',
            '<div class="\1"><pre>\2</pre></div>',
            \SqlFormatter::format($sql)
        );
    }

    public function highlightQuery(string $sql): ?string
    {
        $cacheKey = md5('h' . $sql); // phpcs:ignore Magento2.Security.InsecureFunction.FoundWithAlternative
        if (isset($this->formatterCache[$cacheKey])) {
            return $this->formatterCache[$cacheKey];
        }

        return $this->formatterCache[$cacheKey] = preg_replace(
            '/<pre class=".*">([^"]*+)<\/pre>/Us',
            '\1',
            \SqlFormatter::highlight($sql)
        );
    }

    /**
     * @param string $query
     * @param array $parameters
     * @return mixed
     */
    public function replaceQueryParameters(string $query, array $parameters)
    {
        $i = !array_key_exists(0, $parameters) && array_key_exists(1, $parameters) ? 1 : 0;

        $result = preg_replace_callback('/\?|((?<!:):[a-zA-Z]\w*)/i', function ($matches) use ($parameters, &$i) {
            $value  = isset($parameters[$i])
                ? $parameters[$i]
                : (isset($parameters[$matches[0]]) ? $parameters[$matches[0]] : '?');
            $result = $this->resourceConnection->getConnection(ResourceConnection::DEFAULT_CONNECTION)->quote($value);
            $i++;
            return $result;
        }, $query);

        return $result;
    }
}
