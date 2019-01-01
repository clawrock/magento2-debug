<?php

namespace ClawRock\Debug\Model\ValueObject;

use ClawRock\Debug\Logger\LoggableInterface;

class CacheAction implements LoggableInterface
{
    const LOAD   = 'load';
    const SAVE   = 'save';
    const REMOVE = 'remove';
    const CLEAN  = 'clean';

    const CACHE_ID     = 'id';
    const CACHE_TAGS   = 'tags';
    const CACHE_TIME   = 'time';
    const CACHE_HIT    = 'hit';
    const CACHE_TTL    = 'ttl';
    const CACHE_ACTION = 'action';
    const CACHE_INFO   = 'info';

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var float
     */
    private $time;

    /**
     * @var array
     */
    private $info;

    public function __construct(string $id, string $name, float $time, array $info = [])
    {
        $this->id = $id;
        $this->name = $name;
        $this->time = $time;
        $this->info = $info;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function isLoad(): bool
    {
        return $this->name === self::LOAD;
    }

    /**
     * @return float
     */
    public function getTime(): float
    {
        return $this->time;
    }

    /**
     * @return array
     */
    public function getInfo(): array
    {
        return $this->info;
    }

    /**
     * @return bool
     */
    public function isHit(): bool
    {
        return $this->info[self::CACHE_HIT] ?? false;
    }

    /**
     * @return array
     */
    public function getTags(): array
    {
        return $this->info[self::CACHE_TAGS] ?? [];
    }

    public function hasTags(): bool
    {
        return !empty($this->getTags());
    }

    public function getTTL(): int
    {
        return (int) $this->info[self::CACHE_TTL] ?? 0;
    }
}
