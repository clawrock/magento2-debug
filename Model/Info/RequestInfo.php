<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\Info;

use ClawRock\Debug\Model\Collector\RequestCollector;
use ClawRock\Debug\Model\ValueObject\Redirect;
use Laminas\Stdlib\Parameters;
use Laminas\Stdlib\ParametersInterface;

class RequestInfo
{
    public const TOKEN = 'token';
    public const DEFAULT_CONTENT_TYPE = 'text/html';
    public const PASSWORD_PLACEHOLDER = '******';
    public const REDIRECT_PARAM = 'cdbg_redirect';

    private \ClawRock\Debug\Model\Storage\HttpStorage $httpStorage;
    private \ClawRock\Debug\Model\Session $session;

    public function __construct(
        \ClawRock\Debug\Model\Storage\HttpStorage $httpStorage,
        \ClawRock\Debug\Model\Session $session
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
        if (!$headers instanceof \Laminas\Http\Headers) {
            return new Parameters();
        }
        $authHeader = $headers->get('php-auth-pw');
        if ($authHeader instanceof \Laminas\Http\Header\HeaderInterface) {
            $headers->removeHeader($authHeader);
        }
        if ($authHeader instanceof \ArrayIterator) {
            foreach ($authHeader as $header) {
                $headers->removeHeader($header);
            }
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
        $request = $this->httpStorage->getRequest();

        return new Parameters([
            RequestCollector::REQUEST_STRING => $request->getRequestString(),
            RequestCollector::REQUEST_URI => $request->getRequestUri(),
            RequestCollector::CONTROLLER_MODULE => $request instanceof \Magento\Framework\App\Request\Http
                ? $request->getControllerModule()
                : '',
            RequestCollector::CONTROLLER_NAME => ucwords((string) $request->getControllerName()),
            RequestCollector::ACTION_NAME => ucwords((string) $request->getActionName()),
            RequestCollector::FULL_ACTION_NAME => $request instanceof \Magento\Framework\App\Request\Http
                ? $request->getFullActionName()
                : '',
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
        if ($header instanceof \Laminas\Http\Header\HeaderInterface) {
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
        return $_SESSION ?? []; // phpcs:ignore Magento2.Security.Superglobal.SuperglobalUsageError
    }

    public function getPathInfo(): string
    {
        return $this->httpStorage->getRequest()->getPathInfo();
    }

    public function isFPCRequest(): bool
    {
        return $this->httpStorage->isFPCRequest();
    }

    public function getRedirect(): Redirect
    {
        $redirect = Redirect::createFromArray($this->session->getData(self::REDIRECT_PARAM, true));

        if ($this->httpStorage->getResponse()->isRedirect()) {
            $tokenHeader = $this->httpStorage->getResponse()->getHeader('X-Debug-Token');
            $request = $this->httpStorage->getRequest();
            $this->session->setData(self::REDIRECT_PARAM, [
                Redirect::TOKEN => $tokenHeader instanceof \Laminas\Http\Header\HeaderInterface
                    ? $tokenHeader->getFieldValue()
                    : '',
                Redirect::ACTION => $request instanceof \Magento\Framework\App\Request\Http
                    ? $request->getFullActionName()
                    : '',
                Redirect::METHOD => $this->getMethod(),
                Redirect::STATUS_CODE => $this->getStatusCode(),
                Redirect::STATUS_TEXT => $this->getStatusText(),
            ]);
        }

        return $redirect;
    }
}
