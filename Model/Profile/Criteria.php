<?php

namespace ClawRock\Debug\Model\Profile;

use ClawRock\Debug\Model\Profile;
use Magento\Framework\App\RequestInterface;

class Criteria
{
    /**
     * @var string
     */
    private $ip;

    /**
     * @var string
     */
    private $url;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var string
     */
    private $method;

    /**
     * @var \DateTime
     */
    private $start;

    /**
     * @var \DateTime
     */
    private $end;

    /**
     * @var int
     */
    private $statusCode;

    public function __construct(
        string $ip = '',
        string $url = '',
        int $limit = 0,
        string $method = '',
        \DateTime $start = null,
        \DateTime $end = null,
        int $statusCode = null
    ) {
        $this->ip = $ip;
        $this->url = $url;
        $this->limit = $limit;
        $this->method = $method;
        $this->start = $start;
        $this->end = $end;
        $this->statusCode = $statusCode;
    }

    public static function createFromRequest(RequestInterface $request)
    {
        return new Criteria(
            (string) preg_replace('/[^:\d\.]/', '', $request->getParam('ip')),
            (string) $request->getParam('url'),
            (int) $request->getParam('limit'),
            (string) $request->getParam('method'),
            $request->getParam('start') ? new \DateTime($request->getParam('start')) : null,
            $request->getParam('end') ? new \DateTime($request->getParam('end')) : null,
            (int) $request->getParam('status_code')
        );
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return \DateTime
     */
    public function getStart(): \DateTime
    {
        return $this->start;
    }

    /**
     * @return \DateTime
     */
    public function getEnd(): \DateTime
    {
        return $this->end;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function match(array $data)
    {
        $data = array_combine(Profile::INDEX_PROPERTIES, $data);
        foreach ($data as $property => $value) {
            if ($property === 'time' && !$this->matchTime((int) $value)) {
                return false;
            }

            if (!property_exists($this, $property) || empty($this->$property)) {
                continue;
            }

            if ($value != $this->$property) {
                return false;
            }
        }

        return true;
    }

    private function matchTime(int $timestamp): bool
    {
        if ($this->start !== null && $this->start->getTimestamp() > $timestamp) {
            return false;
        }

        if ($this->end !== null && $this->end->getTimestamp() < $timestamp) {
            return false;
        }

        return true;
    }
}
