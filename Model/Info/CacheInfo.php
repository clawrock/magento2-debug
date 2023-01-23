<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\Info;

use ClawRock\Debug\Model\ValueObject\CacheAction;

class CacheInfo
{
    public const STATS_TOTAL = 'stats_total';
    public const STATS_HIT = 'stats_hit';
    public const STATS_MISS = 'stats_miss';
    public const STATS_SAVE = 'stats_save';

    private \Magento\Framework\App\Cache $cache;
    private \Magento\Framework\App\Cache\TypeListInterface $typeList;
    private ?array $stats = null;
    private ?float $totalTime = null;

    public function __construct(
        \Magento\Framework\App\Cache $cache,
        \Magento\Framework\App\Cache\TypeListInterface $typeList
    ) {
        $this->cache = $cache;
        $this->typeList = $typeList;
    }

    public function getBackendClass(): string
    {
        return get_class($this->cache->getFrontend()->getBackend());
    }

    public function getBackendOptions(): array
    {
        $backendOptions = new \ReflectionProperty(\Zend_Cache_Backend::class, '_options');
        $backendOptions->setAccessible(true);

        return $backendOptions->getValue($this->cache->getFrontend()->getBackend());
    }

    public function getCacheList(): array
    {
        return $this->typeList->getTypes();
    }

    public function getInvalidated(): array
    {
        return array_keys($this->typeList->getInvalidated());
    }

    public function getStats(array $cacheLog): array
    {
        if ($this->stats === null) {
            $this->stats = [
                self::STATS_TOTAL => count($cacheLog),
                self::STATS_HIT   => 0,
                self::STATS_MISS  => 0,
                self::STATS_SAVE  => 0,
            ];

            /** @var \ClawRock\Debug\Model\ValueObject\CacheAction $action */
            foreach ($cacheLog as $action) {
                switch ($action->getName()) {
                    case CacheAction::LOAD:
                        if ($action->isHit()) {
                            $this->stats[self::STATS_HIT]++;
                            break;
                        }
                        $this->stats[self::STATS_MISS]++;
                        break;
                    case CacheAction::SAVE:
                        $this->stats[self::STATS_SAVE]++;
                        break;
                }
            }
        }

        return $this->stats;
    }

    public function getTotalTime(array $cacheLog): float
    {
        if ($this->totalTime === null) {
            $this->totalTime = 0;

            /** @var \ClawRock\Debug\Model\ValueObject\CacheAction $action */
            foreach ($cacheLog as $action) {
                $this->totalTime += $action->getTime();
            }
        }

        return $this->totalTime;
    }
}
