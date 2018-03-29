<?php

namespace ClawRock\Debug\Plugin\Collector;

class CacheDataCollectorPlugin
{
    /**
     * @var \ClawRock\Debug\Model\DataCollector\CacheDataCollector
     */
    private $dataCollector;

    public function __construct(
        \ClawRock\Debug\Model\DataCollector\CacheDataCollector $dataCollector
    ) {
        $this->dataCollector = $dataCollector;
    }

    public function aroundLoad(\Magento\Framework\App\Cache $subject, callable $proceed, $identifier)
    {
        $start = microtime(true);
        $result = $proceed($identifier);
        $time = microtime(true) - $start;

        $this->dataCollector->logCacheLoad($identifier, ($result !== false), $time);

        return $result;
    }

    public function aroundSave(\Magento\Framework\App\Cache $subject, callable $proceed, $data, $identifier, $tags = [], $lifeTime = null)
    {
        $start = microtime(true);
        $result = $proceed($data, $identifier, $tags, $lifeTime);
        $time = microtime(true) - $start;

        $this->dataCollector->logCacheSave($identifier, $tags, $lifeTime, $time);

        return $result;
    }

    public function aroundRemove(\Magento\Framework\App\Cache $subject, callable $proceed, $identifier)
    {
        $start = microtime(true);
        $result = $proceed($identifier);
        $time = microtime(true) - $start;

        $this->dataCollector->logCacheRemove($identifier, $time);

        return $result;
    }

    public function aroundClean(\Magento\Framework\App\Cache $subject, callable $proceed, $tags = [])
    {
        $start = microtime(true);
        $result = $proceed($tags);
        $time = microtime(true) - $start;

        $this->dataCollector->logCacheClean($tags, $time);

        return $result;
    }
}
