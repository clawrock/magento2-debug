<?php

namespace ClawRock\Debug\Model\Info;

use ClawRock\Debug\Model\Collector\PluginCollector;
use ClawRock\Debug\Model\ValueObject\Plugin;
use Magento\Framework\Interception\DefinitionInterface;

class PluginInfo
{
    /**
     * @var array
     */
    private $plugins;

    /**
     * @var \Magento\Framework\Interception\PluginList\PluginList
     */
    private $pluginList;

    /**
     * @var \ClawRock\Debug\Helper\Debug
     */
    private $debug;

    public function __construct(
        \Magento\Framework\Interception\PluginList\PluginList $pluginList,
        \ClawRock\Debug\Helper\Debug $debug
    ) {
        $this->pluginList = $pluginList;
        $this->debug = $debug;
    }

    public function getBeforePlugins(): array
    {
        $this->resolvePlugins();

        return $this->plugins[PluginCollector::BEFORE];
    }

    public function getAroundPlugins(): array
    {
        $this->resolvePlugins();

        return $this->plugins[PluginCollector::AROUND];
    }

    public function getAfterPlugins(): array
    {
        $this->resolvePlugins();

        return $this->plugins[PluginCollector::AFTER];
    }

    private function resolvePlugins(): void
    {
        if ($this->plugins !== null) {
            return;
        }

        $reflection = new \ReflectionClass($this->pluginList);
        $processed = $reflection->getProperty('_processed');
        $processed->setAccessible(true);
        $processed = $processed->getValue($this->pluginList);
        $inherited = $reflection->getProperty('_inherited');
        $inherited->setAccessible(true);
        $inherited = $inherited->getValue($this->pluginList);
        $definitionTypes = [
            DefinitionInterface::LISTENER_BEFORE => PluginCollector::BEFORE,
            DefinitionInterface::LISTENER_AROUND => PluginCollector::AROUND,
            DefinitionInterface::LISTENER_AFTER  => PluginCollector::AFTER,
        ];

        foreach ($processed as $plugin => $definition) {
            if (!preg_match('/^(.*?)_(.*?)_(.*)$/', $plugin, $matches)) {
                continue;
            }
            $type = $matches[1];
            $method = $matches[2];

            if ($this->debug->isDebugClass($type)) {
                continue;
            }

            foreach ($definition as $definitionType => $plugins) {
                foreach ((array) $plugins as $name) {
                    if (isset($inherited[$type][$name])) {
                        if ($this->debug->isDebugClass($inherited[$type][$name]['instance'])) {
                            continue;
                        }
                        $this->plugins[$definitionTypes[$definitionType]][$type][] = new Plugin(
                            $inherited[$type][$name]['instance'],
                            $name,
                            $inherited[$type][$name]['sortOrder'],
                            $definitionTypes[$definitionType] . ucfirst($method),
                            $type
                        );
                    }
                }
            }
        }
    }
}
