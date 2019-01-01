<?php

namespace ClawRock\Debug\Model\Collector;

use ClawRock\Debug\Model\ValueObject\Redirect;

class RequestCollector implements CollectorInterface
{
    const NAME = 'request';

    const REQUEST_METHOD     = 'request_method';
    const REQUEST_GET        = 'request_get';
    const REQUEST_POST       = 'request_post';
    const REQUEST_HEADERS    = 'request_headers';
    const REQUEST_SERVER     = 'request_server';
    const REQUEST_COOKIES    = 'request_cookies';
    const REQUEST_ATTRIBUTES = 'request_attributes';

    const RESPONSE_HEADERS = 'response_headers';

    const CONTENT      = 'content';
    const CONTENT_TYPE = 'content_type';

    const STATUS_TEXT    = 'status_text';
    const STATUS_CODE    = 'status_code';

    const SESSION_ATTRIBUTES = 'session_attributes';

    const PATH_INFO = 'path_info';
    const FPC_HIT   = 'fpc_hit';
    const REDIRECT  = 'redirect';

    const REQUEST_STRING    = 'request_string';
    const REQUEST_URI       = 'request_uri';
    const CONTROLLER_MODULE = 'controller_module';
    const CONTROLLER_NAME   = 'controller_name';
    const ACTION_NAME       = 'action_name';
    const FULL_ACTION_NAME  = 'full_action_name';

    /**
     * @var \ClawRock\Debug\Helper\Config
     */
    private $config;

    /**
     * @var \ClawRock\Debug\Model\DataCollector
     */
    private $dataCollector;

    /**
     * @var \ClawRock\Debug\Model\Info\RequestInfo
     */
    private $requestInfo;

    public function __construct(
        \ClawRock\Debug\Helper\Config $config,
        \ClawRock\Debug\Model\DataCollectorFactory $dataCollectorFactory,
        \ClawRock\Debug\Model\Info\RequestInfo $requestInfo
    ) {
        $this->config = $config;
        $this->dataCollector = $dataCollectorFactory->create();
        $this->requestInfo = $requestInfo;
    }

    public function collect(): CollectorInterface
    {
        $this->dataCollector->setData([
            self::REQUEST_METHOD     => $this->requestInfo->getMethod(),
            self::REQUEST_GET        => $this->requestInfo->getRequestGet(),
            self::REQUEST_POST       => $this->requestInfo->getRequestPost(),
            self::REQUEST_HEADERS    => $this->requestInfo->getRequestHeaders(),
            self::REQUEST_SERVER     => $this->requestInfo->getServer(),
            self::REQUEST_COOKIES    => $this->requestInfo->getCookie(),
            self::REQUEST_ATTRIBUTES => $this->requestInfo->getRequestAttributes(),
            self::RESPONSE_HEADERS   => $this->requestInfo->getResponseHeaders(),
            self::CONTENT            => $this->requestInfo->getContent(),
            self::CONTENT_TYPE       => $this->requestInfo->getContentType(),
            self::STATUS_TEXT        => $this->requestInfo->getStatusText(),
            self::STATUS_CODE        => $this->requestInfo->getStatusCode(),
            self::SESSION_ATTRIBUTES => $this->requestInfo->getSessionAttributes(),
            self::PATH_INFO          => $this->requestInfo->getPathInfo(),
            self::FPC_HIT            => $this->requestInfo->isFPCRequest(),
            self::REDIRECT           => $this->requestInfo->getRedirect()
        ]);

        return $this;
    }

    public function getMethod(): string
    {
        return $this->dataCollector->getData(self::REQUEST_METHOD) ?? '';
    }

    public function getRequestGet()
    {
        return $this->dataCollector->getData(self::REQUEST_GET);
    }

    public function getRequestPost()
    {
        return $this->dataCollector->getData(self::REQUEST_POST);
    }

    public function getRequestHeaders()
    {
        return $this->dataCollector->getData(self::REQUEST_HEADERS);
    }

    public function getRequestServer()
    {
        return $this->dataCollector->getData(self::REQUEST_SERVER);
    }

    public function getRequestCookies()
    {
        return $this->dataCollector->getData(self::REQUEST_COOKIES);
    }

    public function getRequestAttributes()
    {
        return $this->dataCollector->getData(self::REQUEST_ATTRIBUTES) ?? [];
    }

    public function getResponseHeaders()
    {
        return $this->dataCollector->getData(self::RESPONSE_HEADERS);
    }

    public function getContent(): string
    {
        return $this->dataCollector->getData(self::CONTENT);
    }

    public function getContentType(): string
    {
        return $this->dataCollector->getData(self::CONTENT_TYPE);
    }

    public function getStatusText(): string
    {
        return $this->dataCollector->getData(self::STATUS_TEXT);
    }

    public function getStatusCode(): int
    {
        return $this->dataCollector->getData(self::STATUS_CODE);
    }

    public function getSessionAttributes(): array
    {
        return $this->dataCollector->getData(self::SESSION_ATTRIBUTES);
    }

    public function hasSessionData(): bool
    {
        return !empty($this->getSessionAttributes());
    }

    public function getPathInfo(): string
    {
        return $this->dataCollector->getData(self::PATH_INFO) ?? '';
    }

    public function isFPCHit(): bool
    {
        return $this->dataCollector->getData(self::FPC_HIT) ?? false;
    }

    public function getRedirect(): Redirect
    {
        return $this->dataCollector->getData(self::REDIRECT) ?? new Redirect();
    }

    public function getRequestString(): string
    {
        return $this->dataCollector->getData(self::REQUEST_ATTRIBUTES)[self::REQUEST_STRING] ?? '';
    }

    public function getRequestUri(): string
    {
        return $this->dataCollector->getData(self::REQUEST_ATTRIBUTES)[self::REQUEST_URI] ?? '';
    }

    public function getControllerModule(): string
    {
        return $this->dataCollector->getData(self::REQUEST_ATTRIBUTES)[self::CONTROLLER_MODULE] ?? '';
    }

    public function getControllerName(): string
    {
        return $this->dataCollector->getData(self::REQUEST_ATTRIBUTES)[self::CONTROLLER_NAME] ?? '';
    }

    public function getActionName(): string
    {
        return $this->dataCollector->getData(self::REQUEST_ATTRIBUTES)[self::ACTION_NAME] ?? '';
    }

    public function getFullActionName(): string
    {
        return $this->dataCollector->getData(self::REQUEST_ATTRIBUTES)[self::FULL_ACTION_NAME] ?? '';
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return true;
    }

    public function getData(): array
    {
        return $this->dataCollector->getData();
    }

    public function setData(array $data): CollectorInterface
    {
        $this->dataCollector->setData($data);

        return $this;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getStatus(): string
    {
        if ($this->getStatusCode() >= 400) {
            return self::STATUS_ERROR;
        }

        if ($this->getStatusCode() >= 300) {
            return self::STATUS_WARNING;
        }

        return self::STATUS_SUCCESS;
    }
}
