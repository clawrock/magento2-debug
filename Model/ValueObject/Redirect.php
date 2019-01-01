<?php

namespace ClawRock\Debug\Model\ValueObject;

class Redirect
{
    const TOKEN = 'token';
    const ACTION = 'action';
    const METHOD = 'method';
    const STATUS_CODE = 'status_code';
    const STATUS_TEXT = 'status_text';

    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $action;

    /**
     * @var string
     */
    private $method;

    /**
     * @var int
     */
    private $statusCode;

    /**
     * @var string
     */
    private $statusText;

    public function __construct(
        string $token = '',
        string $action = '',
        string $method = '',
        int $statusCode = 0,
        string $statusText = ''
    ) {
        $this->token = $token;
        $this->action = $action;
        $this->method = $method;
        $this->statusCode = $statusCode;
        $this->statusText = $statusText;
    }

    public static function createFromArray(array $data = null)
    {
        if ($data === null) {
            return new Redirect();
        }

        return new Redirect(
            $data[self::TOKEN],
            $data[self::ACTION],
            $data[self::METHOD],
            $data[self::STATUS_CODE],
            $data[self::STATUS_TEXT]
        );
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
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return string
     */
    public function getStatusText(): string
    {
        return $this->statusText;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return !(
            $this->getToken()
            && $this->getAction()
            && $this->getMethod()
            && $this->getStatusCode()
            && $this->getStatusText()
        );
    }

    public function toArray(): array
    {
        return [
            self::TOKEN       => $this->getToken(),
            self::ACTION      => $this->getAction(),
            self::METHOD      => $this->getMethod(),
            self::STATUS_CODE => $this->getStatusCode(),
            self::STATUS_TEXT => $this->getStatusText(),
        ];
    }
}
