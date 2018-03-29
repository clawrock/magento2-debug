<?php

namespace ClawRock\Debug\Block\Profiler\Collector;

use ClawRock\Debug\Block\Profiler\Collector;

class Database extends Collector
{
    /**
     * @var array
     */
    private $queries;

    /**
     * @var array
     */
    private $duplicatedQueries;

    /**
     * @var array
     */
    private $queryTypes;

    /**
     * @var \ClawRock\Debug\Block\Profiler\Renderer\QueryRenderer
     */
    protected $renderer;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \ClawRock\Debug\Helper\Profiler $helper,
        \ClawRock\Debug\Block\Profiler\Renderer\TableRenderer $tableRenderer,
        \ClawRock\Debug\Block\Profiler\Renderer\QueryRenderer $renderer,
        array $data = []
    ) {
        parent::__construct($context, $registry, $helper, $tableRenderer, $data);
        $this->renderer = $renderer;
    }

    public function getQueries($type = null)
    {
        if ($this->queries === null) {
            $this->prepareQueryData();
        }

        if ($type === null) {
            return $this->queries;
        }

        return array_filter($this->queries, function($query) use ($type) {
            return $query['type'] === $type;
        });
    }

    public function getDuplicatedQueries()
    {
        if ($this->duplicatedQueries === null) {
            $this->prepareQueryData();
        }

        return array_column($this->duplicatedQueries, 'query');
    }

    public function countDuplicatedQueries()
    {
        return count($this->getDuplicatedQueries());
    }

    public function prepareQueryData()
    {
        $this->queries = [];
        $this->duplicatedQueries = [];
        $this->queryTypes = [
            \Zend_Db_Profiler::SELECT => 0,
            \Zend_Db_Profiler::INSERT => 0,
            \Zend_Db_Profiler::UPDATE => 0,
            \Zend_Db_Profiler::DELETE => 0,
            \Zend_Db_Profiler::QUERY => 0,
        ];

        /** @var \ClawRock\Debug\Model\DataCollector\DatabaseDataCollector $collector */
        $collector = $this->getCollector();

        /** @var array $query */
        foreach ($collector->getQueries() as $query) {
            $query['type'] = $this->processType($query['profile']);
            $this->preRenderQuery($query);
            $this->processDuplicated($query);
            $this->queries[] = $query;
        }

        usort($this->duplicatedQueries, function ($query1, $query2) {
            return $query2['count'] - $query1['count'];
        });

        $this->duplicatedQueries = array_filter($this->duplicatedQueries, function ($item) {
            return $item['count'] > 1;
        });

        return $this;
    }

    public function preRenderQuery(array &$query)
    {
        $query['sql_highlighted'] = $this->renderer->formatQuery($query['profile']->getQuery(), true);
        $query['sql_formatted'] = $this->renderer->formatQuery($query['profile']->getQuery());
        $query['sql_runnable'] = $this->renderer->formatQuery(
            $this->renderer->replaceQueryParameters($query['profile']->getQuery(),
                $query['profile']->getQueryParams()),
            true
        );
    }

    protected function processDuplicated(array $queryData)
    {
        $queryId = $this->getQueryId($queryData['profile']);
        $time = $queryData['profile']->getElapsedSecs();

        if (!isset($this->duplicatedQueries[$queryId])) {
            $this->duplicatedQueries[$queryId] = [
                'id'         => $queryId,
                'count'      => 0,
                'total_time' => 0,
                'query'      => $queryData,
                'params'     => $queryData['profile']->getQueryParams(),
            ];
        }
        $this->duplicatedQueries[$queryId]['count']++;
        $this->duplicatedQueries[$queryId]['total_time'] += $time;
    }

    protected function getQueryId(\Zend_Db_Profiler_Query $queryProfile)
    {
        return md5($queryProfile->getQuery() . implode(',', $queryProfile->getQueryParams()));
    }

    public function getSelectQueries()
    {
        return $this->getQueries(\Zend_Db_Profiler::SELECT);
    }

    public function getInsertQueries()
    {
        return $this->getQueries(\Zend_Db_Profiler::INSERT);
    }

    public function getUpdateQueries()
    {
        return $this->getQueries(\Zend_Db_Profiler::UPDATE);
    }

    public function getDeleteQueries()
    {
        return $this->getQueries(\Zend_Db_Profiler::DELETE);
    }

    public function getOtherQueries()
    {
        return $this->getQueries(\Zend_Db_Profiler::QUERY);
    }

    public function countQuery($type = null)
    {
        if ($type) {
            return $this->queryTypes[$type] ?? 0;
        }

        $count = 0;
        foreach ($this->queryTypes as $queryType) {
            $count += $queryType;
        }

        return $count;
    }

    public function countSelect()
    {
        return $this->countQuery(\Zend_Db_Profiler::SELECT);
    }

    public function countInsert()
    {
        return $this->countQuery(\Zend_Db_Profiler::INSERT);
    }

    public function countDelete()
    {
        return $this->countQuery(\Zend_Db_Profiler::DELETE);
    }

    public function countUpdate()
    {
        return $this->countQuery(\Zend_Db_Profiler::UPDATE);
    }

    public function countOther()
    {
        return $this->countQuery(\Zend_Db_Profiler::QUERY);
    }

    public function renderQueryTable($prefix, array $queries)
    {
        return $this->renderer->setData([
            'queries' => $queries,
            'prefix'  => $prefix . '-',
        ])->toHtml();
    }

    protected function processType(\Zend_Db_Profiler_Query $query)
    {
        $type = $query->getQueryType();

        if (!isset($this->queryTypes[$type])) {
            $this->queryTypes[\Zend_DB_Profiler::QUERY]++;

            return \Zend_DB_Profiler::QUERY;
        }

        $this->queryTypes[$type]++;

        return $type;
    }

    protected function getQueryType($sql)
    {
        $type = explode(' ', $sql, 2);
        $type = reset($type);
        $type = strtolower($type);

        return $type;
    }
}
