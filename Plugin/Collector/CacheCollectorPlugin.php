<?php
declare(strict_types=1);

namespace ClawRock\Debug\Plugin\Collector;

use ClawRock\Debug\Model\ValueObject\CacheAction;
use Magento\Framework\App\Cache;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class CacheCollectorPlugin
{
    private \ClawRock\Debug\Model\Collector\CacheCollector $cacheCollector;

    public function __construct(
        \ClawRock\Debug\Model\Collector\CacheCollector $cacheCollector
    ) {
        $this->cacheCollector = $cacheCollector;
    }

    /**
     * @param \Magento\Framework\App\Cache $subject
     * @param callable $proceed
     * @param string $identifier
     * @return string
     */
    public function aroundLoad(Cache $subject, callable $proceed, $identifier)
    {
        $start = microtime(true);
        $result = $proceed($identifier);
        $time = microtime(true) - $start;
        $this->cacheCollector->log(new CacheAction($identifier, CacheAction::LOAD, $time, [
            CacheAction::CACHE_HIT => ($result !== false),
        ]));

        return $result;
    }

    /**
     * @param \Magento\Framework\App\Cache $subject
     * @param callable $proceed
     * @param string $data
     * @param string $identifier
     * @param array $tags
     * @param int|null $lifeTime
     * @return bool
     */
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

    /**
     * @param \Magento\Framework\App\Cache $subject
     * @param callable $proceed
     * @param string $identifier
     * @return bool
     */
    public function aroundRemove(Cache $subject, callable $proceed, $identifier)
    {
        $start = microtime(true);
        $result = $proceed($identifier);
        $time = microtime(true) - $start;
        $this->cacheCollector->log(new CacheAction($identifier, CacheAction::REMOVE, $time));

        return $result;
    }

    /**
     * @param \Magento\Framework\App\Cache $subject
     * @param callable $proceed
     * @param array $tags
     * @return bool
     */
    public function aroundClean(Cache $subject, callable $proceed, $tags = [])
    {
        $start = microtime(true);
        $result = $proceed($tags);
        $time = microtime(true) - $start;
        $this->cacheCollector->log(new CacheAction('', CacheAction::CLEAN, $time));

        return $result;
    }
}
