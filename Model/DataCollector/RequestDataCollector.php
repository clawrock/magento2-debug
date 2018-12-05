<?php

namespace ClawRock\Debug\Model\DataCollector;

use Magento\Framework\HTTP\PhpEnvironment\Request;
use Magento\Framework\HTTP\PhpEnvironment\Response;

class RequestDataCollector extends AbstractDataCollector
{
    const NAME = 'request';

    const REDIRECT_PARAM = 'cdb_redirect';

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
    const STATUS_SUCCESS = 'green';
    const STATUS_WARNING = 'yellow';
    const STATUS_ERROR   = 'red';

    const SESSION_ATTRIBUTES = 'session_attributes';
    const SESSION_METADATA   = 'session_metadata';

    const PATH_INFO = 'path_info';

    const FPC_HIT = 'fpc_hit';

    const REDIRECT = 'redirect';

    const TOKEN = 'token';

    const REQUEST_STRING    = 'request_string';
    const REQUEST_URI       = 'request_uri';
    const CONTROLLER_MODULE = 'controller_module';
    const CONTROLLER_NAME   = 'controller_name';
    const ACTION_NAME       = 'action_name';
    const FULL_ACTION_NAME  = 'full_action_name';

    /**
     * @var array
     */
    public static $statusTexts = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Reserved for WebDAV advanced collections expired proposal',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates (Experimental)',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
    ];

    /**
     * @var \ClawRock\Debug\Model\Session
     */
    protected $session;

    public function __construct(
        \ClawRock\Debug\Helper\Profiler $helper,
        \ClawRock\Debug\Model\Session $session
    ) {
        parent::__construct($helper);

        $this->session = $session;
    }

    public function isEnabled()
    {
        return $this->helper->isRequestDataCollectorEnabled();
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param \Magento\Framework\HTTP\PhpEnvironment\Request  $request
     * @param \Magento\Framework\HTTP\PhpEnvironment\Response $response
     * @return $this
     */
    public function collect(
        \Magento\Framework\HTTP\PhpEnvironment\Request $request,
        \Magento\Framework\HTTP\PhpEnvironment\Response $response
    ) {
        $responseHeaders = $this->collectResponseHeaders($response);
        $statusCode = $this->detectStatusCode($response);
        $statusText = self::$statusTexts[$statusCode] ?? '';

        $this->data = [
            self::REQUEST_METHOD     => $request->getMethod(),
            self::REQUEST_GET        => $this->collectRequestGet($request),
            self::REQUEST_POST       => $this->collectRequestPost($request),
            self::REQUEST_HEADERS    => $this->collectRequestHeaders($request),
            self::REQUEST_SERVER     => $request->getServer(),
            self::REQUEST_COOKIES    => $request->getCookie(),
            self::REQUEST_ATTRIBUTES => $this->helper->isFPCRequest() ? [] : $this->collectRequestAttributes($request),
            self::RESPONSE_HEADERS   => $responseHeaders,
            self::CONTENT            => $request->getContent(),
            self::CONTENT_TYPE       => $responseHeaders['Content-Type'] ?? 'text/html',
            self::STATUS_TEXT        => $statusText,
            self::STATUS_CODE        => $statusCode,
            self::SESSION_ATTRIBUTES => [],
            self::SESSION_METADATA   => [],
            self::PATH_INFO          => $request->getPathInfo(),
            self::FPC_HIT            => $this->helper->isFPCRequest(),
        ];

        $this->unsetAuth();
        $this->collectRedirect($request, $response);
        $this->collectSession();

        return $this;
    }

    public function getMethod()
    {
        return $this->data[self::REQUEST_METHOD] ?? '';
    }

    public function getRequestGet()
    {
        return new \Zend\Stdlib\Parameters((array) $this->data[self::REQUEST_GET]);
    }

    public function getRequestPost()
    {
        return new \Zend\Stdlib\Parameters($this->data[self::REQUEST_POST]->toArray());
    }

    public function getRequestHeaders()
    {
        return new \Zend\Stdlib\Parameters((array) $this->data[self::REQUEST_HEADERS]);
    }

    public function getRequestServer()
    {
        return new \Zend\Stdlib\Parameters((array) $this->data[self::REQUEST_SERVER]);
    }

    public function getRequestCookies()
    {
        return new \Zend\Stdlib\Parameters((array) $this->data[self::REQUEST_COOKIES]);
    }

    public function getRequestAttributes()
    {
        return new \Zend\Stdlib\Parameters($this->data[self::REQUEST_ATTRIBUTES] ?? []);
    }

    public function getResponseHeaders()
    {
        return new \Zend\Stdlib\Parameters($this->data[self::RESPONSE_HEADERS]);
    }

    public function getContent()
    {
        return $this->data[self::CONTENT];
    }

    public function getContentType()
    {
        return $this->data[self::CONTENT_TYPE];
    }

    public function getStatusText()
    {
        return $this->data[self::STATUS_TEXT];
    }

    public function getStatusCode()
    {
        return $this->data[self::STATUS_CODE];
    }

    public function getStatusColor()
    {
        if ($this->getStatusCode() >= 400) {
            return self::STATUS_ERROR;
        }

        if ($this->getStatusCode() >= 300) {
            return self::STATUS_WARNING;
        }

        return self::STATUS_SUCCESS;
    }

    public function getSessionAttributes()
    {
        return $this->data[self::SESSION_ATTRIBUTES];
    }

    public function getSessionMetadata()
    {
        return $this->data[self::SESSION_METADATA];
    }

    public function hasSessionData()
    {
        return !($this->getSessionAttributes() && $this->getSessionMetadata());
    }

    public function getPathInfo()
    {
        return $this->data[self::PATH_INFO] ?? '';
    }

    public function isFPCHit()
    {
        return $this->data[self::FPC_HIT] ?? false;
    }

    public function getRedirect()
    {
        return $this->data[self::REDIRECT] ?? false;
    }

    public function getRequestString()
    {
        return $this->data[self::REQUEST_ATTRIBUTES][self::REQUEST_STRING] ?? '';
    }

    public function getRequestUri()
    {
        return $this->data[self::REQUEST_ATTRIBUTES][self::REQUEST_URI] ?? '';
    }

    public function getControllerModule()
    {
        return $this->data[self::REQUEST_ATTRIBUTES][self::CONTROLLER_MODULE] ?? '';
    }

    public function getControllerName()
    {
        return $this->data[self::REQUEST_ATTRIBUTES][self::CONTROLLER_NAME] ?? '';
    }

    public function getActionName()
    {
        return $this->data[self::REQUEST_ATTRIBUTES][self::ACTION_NAME] ?? '';
    }

    public function getFullActionName()
    {
        return $this->data[self::REQUEST_ATTRIBUTES][self::FULL_ACTION_NAME] ?? '';
    }


    protected function collectResponseHeaders(Response $response)
    {
        $headers = [];

        /** @var \Zend\Http\Header\HeaderInterface $header */
        foreach ($response->getHeaders() as $header) {
            $headers[$header->getFieldName()] = $header->getFieldValue();
        }

        return $headers;
    }

    protected function collectRequestAttributes(Request $request)
    {
        $attributes = [
            self::REQUEST_STRING    => $request->getRequestString(),
            self::REQUEST_URI       => $request->getRequestUri(),
            self::CONTROLLER_MODULE => $request->getControllerModule(),
            self::CONTROLLER_NAME   => ucwords($request->getControllerName()),
            self::ACTION_NAME       => ucwords($request->getActionName()),
            self::FULL_ACTION_NAME  => $request->getFullActionName(),
        ];

        return $attributes;
    }

    protected function collectRequestHeaders(Request $request)
    {
        $headers = [];

        foreach ($request->getServer() as $key => $value) {
            if (substr($key, 0, 5) !== 'HTTP_') {
                continue;
            }
            $header = str_replace(' ', '-', (str_replace('_', ' ', strtolower(substr($key, 5)))));
            $headers[$header] = $value;
        }

        return $headers;
    }

    protected function detectStatusCode(Response $response)
    {
        $statusCode = $response->getHttpResponseCode();
        /** @var \Zend\Http\Header\HeaderInterface $header */
        foreach ($response->getHeaders() as $header) {
            if (substr($header->getFieldName(), 0, 5) === 'Http/') {
                preg_match('/^[0-9]{3}/', $header->getFieldValue(), $matches);
                if ($matches) {
                    $statusCode = (int)reset($matches);
                }

                break;
            }
        }

        return $statusCode;
    }

    protected function collectRequestGet(Request $request)
    {
        return $request->getQuery() ?: [];
    }

    protected function collectRequestPost(Request $request)
    {
        return $request->getPost() ?: [];
    }

    protected function collectSession()
    {
        $this->data[self::SESSION_ATTRIBUTES] = $_SESSION ?? [];
    }

    protected function unsetAuth()
    {
        if (isset($this->data[self::REQUEST_HEADERS]['php-auth-pw'])) {
            $this->data[self::REQUEST_HEADERS]['php-auth-pw'] = '******';
        }

        if (isset($this->data[self::REQUEST_SERVER]['PHP_AUTH_PW'])) {
            $this->data[self::REQUEST_SERVER]['PHP_AUTH_PW'] = '******';
        }

        if (isset($this->data[self::REQUEST_POST]['_password'])) {
            $this->data[self::REQUEST_POST]['_password'] = '******';
        }
    }

    protected function collectRedirect(Request $request, Response $response)
    {
        if ($request->getParam('_redirected')) {
            $this->data[self::REDIRECT] = $this->session->getData(self::REDIRECT_PARAM, true);
        }

        if ($response->isRedirect()) {
            $this->session->setData(self::REDIRECT_PARAM, [
                self::TOKEN            => $this->helper->getTokenFromResponse($response),
                self::FULL_ACTION_NAME => $this->getFullActionName(),
                self::REQUEST_METHOD   => $request->getMethod(),
                self::STATUS_CODE      => $this->getStatusCode(),
                self::STATUS_TEXT      => $this->getStatusText(),
            ]);
        }
    }
}
