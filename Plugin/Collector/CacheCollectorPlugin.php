<?php

namespace ClawRock\Debug\Plugin\Collector;

use ClawRock\Debug\Model\ValueObject\CacheAction;
use Magento\Framework\App\Cache;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class CacheCollectorPlugin
{
    /**
     * @var \ClawRock\Debug\Model\Collector\CacheCollector
     */
    private $cacheCollector;

    public function __construct(
        \ClawRock\Debug\Model\Collector\CacheCollector $cacheCollector
    ) {
        $this->cacheCollector = $cacheCollector;
    }

    public function aroundLoad(Cache $subject, callable $proceed, $identifier)
    {
        $start = microtime(true);
        $result = $proceed($identifier);
        $time = microtime(true) - $start;
        $this->cacheCollector->log(new CacheAction($identifier, CacheAction::LOAD, $time, [
            CacheAction::CACHE_HIT => ($result !== false)
        ]));

        return $result;
    }

    public function aroundSave(Cache $subject, callable $proceed, $data, $identifier, $tags = [], $lifeTime = null)
    {
        $start = microtime(true);
        $result = $proceed($data, $identifier, $tags, $lifeTime);
        $time = microtime(true) - $start;
        $this->cacheCollector->log(new CacheAction($identifier, CacheAction::SAVE, $time, [
            CacheAction::CACHE_TAGS => $tags,
            CacheAction::CACHE_TTL => $lifeTime,
        ]));

        return $result;
    }

    public function aroundRemove(Cache $subject, callable $proceed, $identifier)
    {
        $start = microtime(true);
        $result = $proceed($identifier);
        $time = microtime(true) - $start;
        $this->cacheCollector->log(new CacheAction($identifier, CacheAction::REMOVE, $time));

        return $result;
    }

    public function aroundClean(Cache $subject, callable $proceed, $tags = [])
    {
        $start = microtime(true);
        $result = $proceed($tags);
        $time = microtime(true) - $start;
        $this->cacheCollector->log(new CacheAction('', CacheAction::CLEAN, $time));

        return $result;
    }
}
