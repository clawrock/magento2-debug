<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\Collector;

class PluginCollector implements CollectorInterface, LateCollectorInterface
{
    public const NAME = 'plugin';
    public const BEFORE = 'before';
    public const AROUND = 'around';
    public const AFTER = 'after';

    private \ClawRock\Debug\Helper\Config $config;
    private \ClawRock\Debug\Model\DataCollector $dataCollector;
    private \ClawRock\Debug\Model\Info\PluginInfo $pluginInfo;

    public function __construct(
        \ClawRock\Debug\Helper\Config $config,
        \ClawRock\Debug\Model\DataCollectorFactory $dataCollectorFactory,
        \ClawRock\Debug\Model\Info\PluginInfo $pluginInfo
    ) {
        $this->config = $config;
        $this->dataCollector = $dataCollectorFactory->create();
        $this->pluginInfo = $pluginInfo;
    }

    public function collect(): CollectorInterface
    {
        return $this;
    }

    public function lateCollect(): LateCollectorInterface
    {
        $this->dataCollector->setData([
            self::BEFORE => $this->pluginInfo->getBeforePlugins(),
            self::AROUND => $this->pluginInfo->getAroundPlugins(),
            self::AFTER => $this->pluginInfo->getAfterPlugins(),
        ]);

        return $this;
    }

    public function hasPlugins(): bool
    {
        return !empty($this->dataCollector->getData(self::BEFORE))
            || !empty($this->dataCollector->getData(self::AROUND))
            || !empty($this->dataCollector->getData(self::AFTER));
    }

    public function getBeforePlugins(): array
    {
        return $this->dataCollector->getData(self::BEFORE) ?? [];
    }

    public function getAroundPlugins(): array
    {
        return $this->dataCollector->getData(self::AROUND) ?? [];
    }

    public function getAfterPlugins(): array
    {
        return $this->dataCollector->getData(self::AFTER) ?? [];
    }

    public function getPluginsCount(): int
    {
        return $this->getBeforePluginsCount() + $this->getAroundPluginsCount() + $this->getBeforePluginsCount();
    }

    public function getBeforePluginsCount(): int
    {
        return array_sum(array_map('count', $this->getBeforePlugins()));
    }

    public function getAroundPluginsCount(): int
    {
        return array_sum(array_map('count', $this->getAroundPlugins()));
    }

    public function getAfterPluginsCount(): int
    {
        return array_sum(array_map('count', $this->getAfterPlugins()));
    }

    public function isEnabled(): bool
    {
        return $this->config->isPluginCollectorEnabled();
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
        return self::STATUS_DEFAULT;
    }
}
