<?php

namespace ClawRock\Debug\Model\Info;

use ClawRock\Debug\Model\Collector\RequestCollector;
use ClawRock\Debug\Model\ValueObject\Redirect;
use Zend\Stdlib\Parameters;
use Zend\Stdlib\ParametersInterface;

class RequestInfo
{
    const TOKEN = 'token';

    const DEFAULT_CONTENT_TYPE = 'text/html';

    const PASSWORD_PLACEHOLDER = '******';
    const REDIRECT_PARAM = 'cdbg_redirect';

    /**
     * @var \ClawRock\Debug\Model\Storage\HttpStorage
     */
    private $httpStorage;

    /**
     * @var \ClawRock\Debug\Model\Session
     */
    private $session;

    public function __construct(
        \ClawRock\Debug\Model\Storage\HttpStorage $httpStorage,
        \ClawRock\Debug\Model\Session\Proxy $session
    ) {
        $this->httpStorage = $httpStorage;
        $this->session = $session;
    }

    public function getRequestGet(): ParametersInterface
    {
        return $this->httpStorage->getRequest()->getQuery();
    }

    public function getRequestPost(): ParametersInterface
    {
        $post = $this->httpStorage->getRequest()->getPost();
        if ($post->get('_password', false) !== false) {
            $post->set('_password', self::PASSWORD_PLACEHOLDER);
        }

        return $post;
    }

    public function getRequestHeaders(): ParametersInterface
    {
        $headers = $this->httpStorage->getRequest()->getHeaders();
        $authHeader = $this->httpStorage->getRequest()->getHeader('php-auth-pw', false);
        if ($authHeader !== false) {
            $headers->removeHeader($authHeader);
        }

        return new Parameters($headers->toArray());
    }

    public function getMethod(): string
    {
        return $this->httpStorage->getRequest()->getMethod();
    }

    public function getServer(): ParametersInterface
    {
        $server = $this->httpStorage->getRequest()->getServer();
        if ($server->get('PHP_AUTH_PW', false) !== false) {
            $server->set('PHP_AUTH_PW', self::PASSWORD_PLACEHOLDER);
        }

        return $server;
    }

    public function getCookie(): ParametersInterface
    {
        return new Parameters((array) $this->httpStorage->getRequest()->getCookie());
    }

    public function getRequestAttributes(): ParametersInterface
    {
        return new Parameters([
            RequestCollector::REQUEST_STRING    => $this->httpStorage->getRequest()->getRequestString(),
            RequestCollector::REQUEST_URI       => $this->httpStorage->getRequest()->getRequestUri(),
            RequestCollector::CONTROLLER_MODULE => $this->httpStorage->getRequest()->getControllerModule(),
            RequestCollector::CONTROLLER_NAME   => ucwords($this->httpStorage->getRequest()->getControllerName()),
            RequestCollector::ACTION_NAME       => ucwords($this->httpStorage->getRequest()->getActionName()),
            RequestCollector::FULL_ACTION_NAME  => $this->httpStorage->getRequest()->getFullActionName(),
        ]);
    }

    public function getResponseHeaders(): ParametersInterface
    {
        return new Parameters($this->httpStorage->getResponse()->getHeaders()->toArray());
    }

    public function getContent(): string
    {
        return $this->httpStorage->getRequest()->getContent();
    }

    public function getContentType(): string
    {
        $header = $this->httpStorage->getResponse()->getHeader('Content-Type');
        if ($header instanceof \Zend\Http\Header\HeaderInterface) {
            return $header->getFieldValue();
        }

        return self::DEFAULT_CONTENT_TYPE;
    }

    public function getStatusText(): string
    {
        return $this->httpStorage->getResponse()->getReasonPhrase();
    }

    public function getStatusCode(): int
    {
        return $this->httpStorage->getResponse()->getStatusCode();
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     * @return array
     */
    public function getSessionAttributes(): array
    {
        return $_SESSION;
    }

    public function getPathInfo(): string
    {
        return $this->httpStorage->getRequest()->getPathInfo();
    }

    public function isFPCRequest(): bool
    {
        return $this->httpStorage->isFPCRequest();
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @return \ClawRock\Debug\Model\ValueObject\Redirect
     */
    public function getRedirect(): Redirect
    {
        $redirect = Redirect::createFromArray($this->session->getData(self::REDIRECT_PARAM, true));

        if ($this->httpStorage->getResponse()->isRedirect()) {
            $this->session->setData(self::REDIRECT_PARAM, [
                Redirect::TOKEN       => $this->httpStorage->getResponse()->getHeader('X-Debug-Token')->getFieldValue(),
                Redirect::ACTION      => $this->httpStorage->getRequest()->getFullActionName(),
                Redirect::METHOD      => $this->getMethod(),
                Redirect::STATUS_CODE => $this->getStatusCode(),
                Redirect::STATUS_TEXT => $this->getStatusText(),
            ]);
        }

        return $redirect;
    }
}
