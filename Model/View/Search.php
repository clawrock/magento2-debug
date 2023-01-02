<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\View;

use ClawRock\Debug\Model\Collector\RequestCollector;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Search implements ArgumentInterface
{
    private \Magento\Framework\App\RequestInterface $request;
    private \ClawRock\Debug\Model\Storage\ProfileMemoryStorage $profileMemoryStorage;
    private ?string $token = null;
    private \ClawRock\Debug\Helper\Formatter $formatter;
    private \ClawRock\Debug\Helper\Url $url;

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

    public function isParamSelected(string $param, string $expected): bool
    {
        return $this->request->getParam($param) === $expected;
    }

    public function getParam(string $param): ?string
    {
        return $this->request->getParam($param);
    }

    public function getLimits(): array
    {
        return ['10', '50', '100'];
    }

    public function getMethods(): array
    {
        return ['GET', 'POST', 'DELETE', 'PUT', 'PATCH', 'HEAD'];
    }

    public function getToken(): string
    {
        if ($this->token === null) {
            $this->token = $this->profileMemoryStorage->read()->getToken();
        }

        return $this->token;
    }

    public function toMegaBytes(int $value): string
    {
        return $this->formatter->toMegaBytes($value, 2);
    }

    public function getProfilerUrl(string $token): string
    {
        return $this->url->getProfilerUrl($token, RequestCollector::NAME);
    }
}
