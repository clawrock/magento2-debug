<?php

namespace ClawRock\Debug\Model;

use ClawRock\Debug\Model\DataCollector\DataCollectorInterface;
use Magento\Framework\DataObject;

class Profile extends DataObject
{
    /**
     * @var string
     */
    protected $token;

    /**
     * @var array
     */
    protected $collectors = [];

    /**
     * @var string
     */
    protected $ip;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var int
     */
    protected $time;

    /**
     * @var int
     */
    protected $statusCode;

    /**
     * @var int
     */
    protected $collectTime;

    /**
     * @var \ClawRock\Debug\Model\Profile
     */
    protected $parent;

    /**
     * @var array
     */
    protected $children = [];

    public function __construct($token, array $data = [])
    {
        $this->token = $token;

        parent::__construct($data);
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setParent(Profile $parent)
    {
        $this->parent = $parent;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function getParentToken()
    {
        return $this->parent ? $this->parent->getToken() : null;
    }

    public function getIp()
    {
        return $this->ip;
    }

    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function setMethod($method)
    {
        $this->method = $method;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getTime()
    {
        if (null === $this->time) {
            return 0;
        }

        return $this->time;
    }

    public function setTime($time)
    {
        $this->time = $time;
    }

    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getCollectTime()
    {
        return $this->collectTime;
    }

    public function setCollectTime($time)
    {
        return $this->collectTime = $time;
    }

    public function getChildren()
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

    public function addChild(Profile $child)
    {
        $this->children[] = $child;
        $child->setParent($this);
    }

    public function getCollector($name)
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

    public function addCollector(DataCollectorInterface $collector)
    {
        $this->collectors[$collector->getCollectorName()] = $collector;

        return $this;
    }

    public function hasCollector($name)
    {
        return isset($this->collectors[$name]);
    }

    public function __sleep()
    {
        return ['token', 'parent', 'children', 'collectors', 'ip', 'method', 'url', 'time', 'statusCode', 'collectTime'];
    }
}
