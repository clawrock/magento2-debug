<?php

namespace ClawRock\Debug\Model;

use ClawRock\Debug\Api\Data\ProfileInterface;
use ClawRock\Debug\Model\Collector\CollectorInterface;
use ClawRock\Debug\Model\Collector\RequestCollector;
use ClawRock\Debug\Model\ValueObject\Redirect;

class Profile implements ProfileInterface
{
    const SERIALIZE_PROPERTIES = [
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

    const INDEX_PROPERTIES = [
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

    /**
     * @var string
     */
    private $token;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * @var array
     */
    private $collectors = [];

    /**
     * @var string
     */
    private $ip;

    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $route;

    /**
     * @var string
     */
    private $url;

    /**
     * @var int
     */
    private $time;

    /**
     * @var int
     */
    private $statusCode;

    /**
     * @var int
     */
    private $collectTime;

    /**
     * @var \ClawRock\Debug\Model\Profile
     */
    private $parent;

    /**
     * @var string
     */
    private $parentToken;

    /**
     * @var array
     */
    private $children = [];

    /**
     * @var int
     */
    private $fileSize;

    /**
     * @var float
     */
    private $requestTime = 0;

    public function __construct(
        $token,
        \Magento\Framework\UrlInterface $urlBuilder
    ) {
        $this->token = $token;
        $this->urlBuilder = $urlBuilder;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setParent(ProfileInterface $parent)
    {
        $this->parent = $parent;
        $this->parentToken = $parent->getToken();
    }

    public function getParent(): ProfileInterface
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

    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    public function getMethod(): string
    {
        return strtoupper($this->method);
    }

    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getRoute(): string
    {
        return $this->route;
    }

    /**
     * @param string $route
     */
    public function setRoute(string $route)
    {
        $this->route = $route;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getTime(): string
    {
        return date('r', $this->time);
    }

    public function setTime($time)
    {
        $this->time = $time;
    }

    public function setStatusCode(int $statusCode)
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

    public function setCollectTime($time)
    {
        return $this->collectTime = $time;
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function setChildren(array $children)
    {
        $this->children = [];
        foreach ($children as $child) {
            $this->addChild($child);
        }
    }

    public function addChild(ProfileInterface $child)
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

    public function getCollectors()
    {
        return $this->collectors;
    }

    public function setCollectors(array $collectors)
    {
        $this->collectors = [];
        foreach ($collectors as $collector) {
            $this->addCollector($collector);
        }
    }

    public function addCollector(CollectorInterface $collector)
    {
        $this->collectors[$collector->getName()] = $collector;

        return $this;
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

    /**
     * @return array
     */
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

    /**
     * @param int $fileSize
     * @return \ClawRock\Debug\Api\Data\ProfileInterface
     */
    public function setFileSize(int $fileSize): ProfileInterface
    {
        $this->fileSize = $fileSize;

        return $this;
    }

    /**
     * @param string $requestTime
     * @return \ClawRock\Debug\Api\Data\ProfileInterface
     */
    public function setRequestTime(string $requestTime): ProfileInterface
    {
        $this->requestTime = $requestTime;

        return $this;
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
