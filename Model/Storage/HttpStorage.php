<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\Storage;

use Magento\Framework\HTTP\PhpEnvironment\Request;
use Magento\Framework\HTTP\PhpEnvironment\Response;

class HttpStorage
{
    private \Magento\Framework\HTTP\PhpEnvironment\Request $request;
    private \Magento\Framework\HTTP\PhpEnvironment\Response $response;
    private bool $fpc = false;

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function setRequest(Request $request): HttpStorage
    {
        $this->request = $request;

        return $this;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

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
