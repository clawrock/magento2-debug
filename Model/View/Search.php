<?php

namespace ClawRock\Debug\Model\View;

use ClawRock\Debug\Model\Collector\RequestCollector;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Search implements ArgumentInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \ClawRock\Debug\Model\Storage\ProfileMemoryStorage
     */
    private $profileMemoryStorage;

    /**
     * @var string
     */
    private $token;

    /**
     * @var \ClawRock\Debug\Helper\Formatter
     */
    private $formatter;

    /**
     * @var \ClawRock\Debug\Helper\Url
     */
    private $url;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \ClawRock\Debug\Model\Storage\ProfileMemoryStorage $profileMemoryStorage,
        \ClawRock\Debug\Helper\Formatter $formatter,
        \ClawRock\Debug\Helper\Url $url
    ) {
        $this->request = $request;
        $this->profileMemoryStorage = $profileMemoryStorage;
        $this->formatter = $formatter;
        $this->url = $url;
    }

    public function isParamSelected($param, $expected): bool
    {
        return $this->request->getParam($param) === $expected;
    }

    public function getParam($param)
    {
        return $this->request->getParam($param);
    }

    public function getLimits()
    {
        return ['10', '50', '100'];
    }

    public function getMethods()
    {
        return ['GET', 'POST', 'DELETE', 'PUT', 'PATCH', 'HEAD'];
    }

    public function getToken()
    {
        if ($this->token === null) {
            $this->token = $this->profileMemoryStorage->read()->getToken();
        }

        return $this->token;
    }

    public function toMegaBytes(int $value)
    {
        return $this->formatter->toMegaBytes($value, 2);
    }

    public function getProfilerUrl(string $token): string
    {
        return $this->url->getProfilerUrl($token, RequestCollector::NAME);
    }
}
