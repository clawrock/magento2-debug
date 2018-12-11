<?php

namespace ClawRock\Debug\Model\ValueObject;

class SearchResult
{
    const STATUS_SUCCESS = 'success';
    const STATUS_WARNING = 'warning';
    const STATUS_ERROR = 'error';

    /**
     * @var string
     */
    private $token;

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
    private $url;

    /**
     * @var int
     */
    private $time;

    /**
     * @var string
     */
    private $statusCode;

    /**
     * @var string
     */
    private $fileSize;

    /**
     * @var string
     */
    private $parentToken;

    /**
     * @var \DateTime
     */
    private $datetime;

    public function __construct(
        string $token,
        string $ip,
        string $method,
        string $url,
        int $time,
        string $statusCode,
        string $fileSize,
        string $parentToken = null
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
    }

    public static function createFromCsv(array $csv)
    {
        return new SearchResult(...$csv);
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
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
    public function getMethod(): string
    {
        return $this->method;
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
    public function getTime(): int
    {
        return $this->time;
    }

    /**
     * @return string
     */
    public function getStatusCode(): string
    {
        return $this->statusCode;
    }

    /**
     * @return string
     */
    public function getFileSize(): string
    {
        return $this->fileSize;
    }

    /**
     * @return string
     */
    public function getParentToken(): string
    {
        return $this->parentToken;
    }

    /**
     * @return \DateTime
     */
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
