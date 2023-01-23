<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model;

use ClawRock\Debug\Api\Data\ProfileInterface;
use ClawRock\Debug\Model\Collector\CollectorInterface;
use ClawRock\Debug\Model\Collector\RequestCollector;
use ClawRock\Debug\Model\ValueObject\Redirect;

class Profile implements ProfileInterface
{
    public const SERIALIZE_PROPERTIES = [
        'token',
        'parent',
        'ip',
        'method',
        'route',
        'url',
        'time',
        'statusCode',
        'collectTime',
        'children',
    ];

    public const INDEX_PROPERTIES = [
        'token',
        'ip',
        'method',
        'url',
        'time',
        'statusCode',
        'fileSize',
        'parentToken',
        'requestTime',
    ];

    private string $token;
    private array $collectors = [];
    private string $ip;
    private string $method;
    private string $route;
    private string $url;
    private int $time;
    private int $statusCode;
    private float $collectTime;
    private ?\ClawRock\Debug\Api\Data\ProfileInterface $parent = null;
    private ?string $parentToken = null;
    private array $children = [];
    private int $fileSize;
    private string $requestTime = '0.0';

    public function __construct(
        string $token
    ) {
        $this->token = $token;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setParent(ProfileInterface $parent): void
    {
        $this->parent = $parent;
        $this->parentToken = $parent->getToken();
    }

    public function getParent(): ?ProfileInterface
    {
        return $this->parent;
    }

    public function getParentToken(): string
    {
        return (string) $this->parentToken;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function setIp(string $ip): void
    {
        $this->ip = $ip;
    }

    public function getMethod(): string
    {
        return strtoupper($this->method);
    }

    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function setRoute(string $route): void
    {
        $this->route = $route;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getTime(): string
    {
        return date('r', $this->time);
    }

    public function setTime(int $time): void
    {
        $this->time = $time;
    }

    public function setStatusCode(int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getCollectTime(): float
    {
        return $this->collectTime;
    }

    public function setCollectTime(float $time): void
    {
        $this->collectTime = $time;
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function setChildren(array $children): void
    {
        $this->children = [];
        foreach ($children as $child) {
            $this->addChild($child);
        }
    }

    public function addChild(ProfileInterface $child): void
    {
        $this->children[] = $child;
        $child->setParent($this);
    }

    public function getCollector(string $name): CollectorInterface
    {
        if (!isset($this->collectors[$name])) {
            throw new \InvalidArgumentException(sprintf('Collector "%s" does not exist.', $name));
        }

        return $this->collectors[$name];
    }

    public function getCollectors(): array
    {
        return $this->collectors;
    }

    public function setCollectors(array $collectors): void
    {
        $this->collectors = [];
        foreach ($collectors as $collector) {
            $this->addCollector($collector);
        }
    }

    public function addCollector(CollectorInterface $collector): void
    {
        $this->collectors[$collector->getName()] = $collector;
    }

    public function hasCollector(string $name): bool
    {
        return isset($this->collectors[$name]);
    }

    public function getIndex(): array
    {
        $data = [];

        foreach (self::INDEX_PROPERTIES as $property) {
            $data[] = $this->$property;
        }

        return $data;
    }

    public function getData(): array
    {
        $data = [];

        foreach (self::SERIALIZE_PROPERTIES as $property) {
            $data[$property] = $this->$property;
        }

        return $data;
    }

    /**
     * @param array $data
     * @return \ClawRock\Debug\Model\Profile
     */
    public function setData(array $data): ProfileInterface
    {
        foreach (self::SERIALIZE_PROPERTIES as $property) {
            $this->$property = $data[$property];
        }

        return $this;
    }

    public function getFileSize(): int
    {
        return $this->fileSize;
    }

    public function setFileSize(int $fileSize): ProfileInterface
    {
        $this->fileSize = $fileSize;

        return $this;
    }

    public function getRequestTime(): string
    {
        return $this->requestTime;
    }

    public function setRequestTime(string $requestTime): void
    {
        $this->requestTime = $requestTime;
    }

    public function getStatus(): string
    {
        if ($this->getStatusCode() >= 400) {
            return CollectorInterface::STATUS_ERROR;
        }

        if ($this->getStatusCode() >= 300) {
            return CollectorInterface::STATUS_WARNING;
        }

        return CollectorInterface::STATUS_SUCCESS;
    }

    public function hasRedirect(): bool
    {
        return !$this->getRedirect()->isEmpty();
    }

    public function getRedirect(): Redirect
    {
        /** @var \ClawRock\Debug\Model\Collector\RequestCollector $collector */
        $collector = $this->getCollector(RequestCollector::NAME);

        return $collector->getRedirect();
    }
}
