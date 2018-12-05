<?php

namespace ClawRock\Debug\Model\DataCollector;

class CacheDataCollector extends AbstractDataCollector implements LateDataCollectorInterface
{
    const NAME            = 'cache';
    const ACTION_LOAD     = 'load';
    const ACTION_SAVE     = 'save';
    const ACTION_REMOVE   = 'remove';
    const ACTION_CLEAN    = 'clean';
    const BACKEND_NAME    = 'backend_name';
    const BACKEND_OPTIONS = 'backend_options';
    const CACHE_LIST      = 'cache_list';
    const INVALIDATED     = 'invalidated_types';
    const CACHE_LOG       = 'cache_log';
    const STATS           = 'stats';
    const STATS_TOTAL     = 'stats_total';
    const STATS_HIT       = 'stats_hit';
    const STATS_MISS      = 'stats_miss';
    const STATS_SAVE      = 'stats_save';
    const TOTAL_TIME      = 'total_time';
    const CACHE_STATUS    = 'status';
    const CACHE_ID        = 'id';
    const CACHE_TAGS      = 'tags';
    const CACHE_TIME      = 'time';
    const CACHE_HIT       = 'hit';
    const CACHE_TTL       = 'ttl';
    const CACHE_ACTION    = 'action';

    /**
     * @var \Magento\Framework\App\Cache
     */
    protected $cache;

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $typeList;

    /**
     * @var array
     */
    protected $log = [];

    public function __construct(
        \ClawRock\Debug\Helper\Profiler $helper,
        \Magento\Framework\App\Cache $cache,
        \Magento\Framework\App\Cache\TypeListInterface $typeList
    ) {
        parent::__construct($helper);

        $this->cache = $cache;
        $this->typeList = $typeList;
    }

    public function isEnabled()
    {
        return $this->helper->isCacheDataCollectorEnabled();
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
        $caches = [];

        foreach ($this->typeList->getTypes() as $type) {
            $caches[$type->getId()] = $type->getData();
        }

        $cache = $this->getCache();
        $backend = $cache->getBackend();

        $backendOptionsProperty = new \ReflectionProperty(\Zend_Cache_Backend::class, '_options');
        $backendOptionsProperty->setAccessible(true);

        $this->data = [
            self::BACKEND_NAME => get_class($backend),
            self::BACKEND_OPTIONS => $backendOptionsProperty->getValue($backend),
            self::CACHE_LIST => $caches,
            self::INVALIDATED => array_keys($this->typeList->getInvalidated()),
            self::CACHE_LOG => [],
            self::STATS => [
                self::STATS_TOTAL => 0,
                self::STATS_HIT => 0,
                self::STATS_MISS => 0,
                self::STATS_SAVE => 0,
            ],
        ];

        return $this;
    }

    protected function getCache()
    {
        return $this->cache->getFrontend();
    }

    public function lateCollect()
    {
        $totalTime = 0;
        $stats = [
            self::STATS_TOTAL => count($this->log),
            self::STATS_HIT   => 0,
            self::STATS_MISS  => 0,
            self::STATS_SAVE  => 0,
        ];

        foreach ($this->log as $log) {
            $totalTime += $log[self::CACHE_TIME];
            switch ($log[self::CACHE_ACTION]) {
                case self::ACTION_LOAD:
                    $stats[$log[self::STATS_HIT] ? self::STATS_HIT : self::STATS_MISS]++;
                    break;
                case self::ACTION_SAVE:
                    $stats[self::STATS_SAVE]++;
                    break;
                default:
                    break;
            }
        }

        $this->data[self::STATS] = $stats;
        $this->data[self::TOTAL_TIME] = round($totalTime * 1000, 3);
        $this->data[self::CACHE_LOG] = $this->log;
    }

    public function logCacheLoad($id, $hit, $time)
    {
        $this->log[] = [
            self::CACHE_ACTION => self::ACTION_LOAD,
            self::CACHE_ID     => $id,
            self::STATS_HIT    => $hit,
            self::CACHE_TIME   => $time,
        ];

        return $this;
    }

    public function logCacheSave($id, $tags, $ttl, $time)
    {
        $this->log[] = [
            self::CACHE_ACTION => self::ACTION_SAVE,
            self::CACHE_ID     => $id,
            self::CACHE_TAGS   => $tags,
            self::CACHE_TTL    => $ttl,
            self::CACHE_TIME   => $time,
        ];

        return $this;
    }

    public function logCacheRemove($id, $time)
    {
        $this->log[] = [
            self::CACHE_ACTION => self::ACTION_REMOVE,
            self::CACHE_ID     => $id,
            self::CACHE_TIME   => $time,
        ];

        return $this;
    }

    public function logCacheClean($tags, $time)
    {
        $this->log[] = [
            self::CACHE_ACTION => self::ACTION_CLEAN,
            self::CACHE_TAGS   => $tags,
            self::CACHE_TIME   => $time,
        ];

        return $this;
    }

    public function getBackendName()
    {
        return $this->data[self::BACKEND_NAME] ?? 'Unknown';
    }

    public function getBackendOptions()
    {
        return $this->data[self::BACKEND_OPTIONS] ?? [];
    }

    public function getStats($key = null)
    {
        if ($key) {
            return $this->data[self::STATS][$key] ?? null;
        }

        return $this->data[self::STATS] ?? [];
    }

    public function getTotalTime()
    {
        return $this->data[self::TOTAL_TIME] ?? 0;
    }

    public function getCacheList()
    {
        return $this->data[self::CACHE_LIST] ?? [];
    }

    public function getCacheCalls()
    {
        return $this->data[self::CACHE_LOG] ?? [];
    }

    public function isCacheTypeEnabled($type)
    {
        return $this->data[self::CACHE_LIST][$type][self::CACHE_STATUS] ?? false;
    }

    public function isInvalidated($type)
    {
        return in_array($type, $this->data[self::INVALIDATED]);
    }
}
