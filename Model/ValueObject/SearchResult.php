<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\ValueObject;

class SearchResult
{
    public const STATUS_SUCCESS = 'success';
    public const STATUS_WARNING = 'warning';
    public const STATUS_ERROR = 'error';

    private string $token;
    private string $ip;
    private string $method;
    private string $url;
    private int $time;
    private string $statusCode;
    private string $fileSize;
    private string $requestTime;
    private ?string $parentToken;
    private \DateTime $datetime;

    public function __construct(
        string $token,
        string $ip,
        string $method,
        string $url,
        int $time,
        string $statusCode,
        string $fileSize,
        ?string $parentToken = null,
        string $requestTime = '0'
    ) {
        $this->token = $token;
        $this->ip = $ip;
        $this->method = $method;
        $this->url = $url;
        $this->time = $time;
        $this->statusCode = $statusCode;
        $this->parentToken = $parentToken;
        $this->datetime = (new \DateTime())->setTimestamp($time);
        $this->fileSize = $fileSize;
        $this->requestTime = $requestTime;
    }

    // phpcs:ignore Magento2.Functions.StaticFunction.StaticFunction
    public static function createFromCsv(array $csv): SearchResult
    {
        [$token, $ip, $method, $url, $time, $statusCode, $fileSize, $parentToken, $requestTime] = $csv;
        return new SearchResult(
            $token,
            $ip,
            $method,
            $url,
            (int) $time,
            $statusCode,
            $fileSize,
            $parentToken ?: null,
            $requestTime
        );
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getTime(): int
    {
        return $this->time;
    }

    public function getStatusCode(): string
    {
        return $this->statusCode;
    }

    public function getFileSize(): string
    {
        return $this->fileSize;
    }

    public function getRequestTime(): string
    {
        return $this->requestTime;
    }

    public function getParentToken(): ?string
    {
        return $this->parentToken;
    }

    public function getDatetime(): \DateTime
    {
        return $this->datetime;
    }

    public function getStatus(): string
    {
        if ($this->statusCode > 399) {
            return self::STATUS_ERROR;
        }

        if ($this->statusCode > 299) {
            return self::STATUS_WARNING;
        }

        return self::STATUS_SUCCESS;
    }
}
