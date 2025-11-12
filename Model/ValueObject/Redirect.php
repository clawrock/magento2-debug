<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\ValueObject;

class Redirect
{
    public const TOKEN = 'token';
    public const ACTION = 'action';
    public const METHOD = 'method';
    public const STATUS_CODE = 'status_code';
    public const STATUS_TEXT = 'status_text';

    public function __construct(
        private string $token = '',
        private string $action = '',
        private string $method = '',
        private int $statusCode = 0,
        private string $statusText = ''
    ) {
    }

    // phpcs:ignore Magento2.Functions.StaticFunction.StaticFunction
    public static function createFromArray(?array $data = null): Redirect
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

    public function getToken(): string
    {
        return $this->token;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getStatusText(): string
    {
        return $this->statusText;
    }

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
