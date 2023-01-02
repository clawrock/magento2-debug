<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\Profile;

use ClawRock\Debug\Model\Profile;
use Magento\Framework\App\RequestInterface;

class Criteria
{
    private string $ip;
    private string $url;
    private int $limit;
    private string $method;
    private ?\DateTime $start;
    private ?\DateTime $end;
    private ?int $statusCode;

    public function __construct(
        string $ip = '',
        string $url = '',
        int $limit = 0,
        string $method = '',
        ?\DateTime $start = null,
        ?\DateTime $end = null,
        ?int $statusCode = null
    ) {
        $this->ip = $ip;
        $this->url = $url;
        $this->limit = $limit;
        $this->method = $method;
        $this->start = $start;
        $this->end = $end;
        $this->statusCode = $statusCode;
    }

    // phpcs:ignore Magento2.Functions.StaticFunction.StaticFunction
    public static function createFromRequest(RequestInterface $request): Criteria
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

    public function getIp(): string
    {
        return $this->ip;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getStart(): ?\DateTime
    {
        return $this->start;
    }

    public function getEnd(): ?\DateTime
    {
        return $this->end;
    }

    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    public function match(array $data): bool
    {
        try {
            $data = array_combine(Profile::INDEX_PROPERTIES, $data);
        } catch (\Throwable $e) {
            $data = false;
        }

        if ($data === false) {
            return false;
        }

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
