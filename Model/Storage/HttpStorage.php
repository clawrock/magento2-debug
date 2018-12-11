<?php

namespace ClawRock\Debug\Model\Storage;

use Magento\Framework\HTTP\PhpEnvironment\Request;
use Magento\Framework\HTTP\PhpEnvironment\Response;

class HttpStorage
{
    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\Request
     */
    private $request;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\Response
     */
    private $response;

    /**
     * @var bool
     */
    private $fpc = false;

    /**
     * @return \Magento\Framework\HTTP\PhpEnvironment\Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @param \Magento\Framework\HTTP\PhpEnvironment\Request $request
     * @return HttpStorage
     */
    public function setRequest(Request $request): HttpStorage
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return \Magento\Framework\HTTP\PhpEnvironment\Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * @param \Magento\Framework\HTTP\PhpEnvironment\Response $response
     * @return HttpStorage
     */
    public function setResponse(Response $response): HttpStorage
    {
        $this->response = $response;

        return $this;
    }

    public function markAsFPCRequest(): HttpStorage
    {
        $this->fpc = true;

        return $this;
    }

    public function isFPCRequest(): bool
    {
        return $this->fpc;
    }
}
