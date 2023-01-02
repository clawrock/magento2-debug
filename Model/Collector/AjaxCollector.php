<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\Collector;

class AjaxCollector implements CollectorInterface
{
    const NAME = 'ajax';

    private \ClawRock\Debug\Helper\Config $config;

    public function __construct(
        \ClawRock\Debug\Helper\Config $config
    ) {
        $this->config = $config;
    }

    public function collect(): CollectorInterface
    {
        // Nothing to collect here
        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->config->isAjaxCollectorEnabled();
    }

    public function getData(): array
    {
        return [];
    }

    public function setData(array $data): CollectorInterface
    {
        return $this;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getStatus(): string
    {
        return self::STATUS_DEFAULT;
    }
}
