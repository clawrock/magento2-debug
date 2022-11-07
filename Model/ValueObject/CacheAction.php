<?php
declare(strict_types=1);

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

    private string $id;
    private string $name;
    private float $time;
    private array $info;

    public function __construct(string $id, string $name, float $time, array $info = [])
    {
        $this->id = $id;
        $this->name = $name;
        $this->time = $time;
        $this->info = $info;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isLoad(): bool
    {
        return $this->name === self::LOAD;
    }

    public function getTime(): float
    {
        return $this->time;
    }

    public function getInfo(): array
    {
        return $this->info;
    }

    public function isHit(): bool
    {
        return $this->info[self::CACHE_HIT] ?? false;
    }

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
        return (int) ($this->info[self::CACHE_TTL] ?? 0);
    }
}
