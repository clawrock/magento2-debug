<?php

namespace ClawRock\Debug\Model\DataCollector;

use Magento\Framework\Interception\DefinitionInterface;

class PluginDataCollector extends AbstractDataCollector
{
    const NAME = 'plugin';

    const BEFORE = 'before';
    const AROUND = 'around';
    const AFTER  = 'after';

    protected $data = [
        self::BEFORE => [],
        self::AROUND => [],
        self::AFTER => [],
    ];

    /**
     * @var \Magento\Framework\Interception\PluginList\PluginList
     */
    private $pluginList;

    /**
     * @var \ReflectionClassFactory
     */
    private $reflectionClassFactory;

    public function __construct(
        \ClawRock\Debug\Helper\Profiler $helper,
        \Magento\Framework\Interception\PluginList\PluginList $pluginList,
        \ReflectionClassFactory $reflectionClassFactory
    ) {
        parent::__construct($helper);
        $this->pluginList = $pluginList;
        $this->reflectionClassFactory = $reflectionClassFactory;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param \Magento\Framework\HTTP\PhpEnvironment\Request  $request
     * @param \Magento\Framework\HTTP\PhpEnvironment\Response $response
     * @return $this
     * @throws \ReflectionException
     */
    public function collect(
        \Magento\Framework\HTTP\PhpEnvironment\Request $request,
        \Magento\Framework\HTTP\PhpEnvironment\Response $response
    ) {
        $reflection = $this->reflectionClassFactory->create(['argument' => $this->pluginList]);
        $processed = $reflection->getProperty('_processed');
        $processed->setAccessible(true);
        $processed = $processed->getValue($this->pluginList);
        $inherited = $reflection->getProperty('_inherited');
        $inherited->setAccessible(true);
        $inherited = $inherited->getValue($this->pluginList);
        $definitionTypes = [
            DefinitionInterface::LISTENER_BEFORE => self::BEFORE,
            DefinitionInterface::LISTENER_AROUND => self::AROUND,
            DefinitionInterface::LISTENER_AFTER  => self::AFTER,
        ];

        foreach ($processed as $plugin => $definition) {
            if (!preg_match('/^(.*?)_(.*?)_(.*)$/', $plugin, $matches)) {
                continue;
            }
            $type = $matches[1];
            $method = $matches[2];

            if ($this->isDebugClass($type)) {
                continue;
            }

            foreach ($definition as $definitionType => $plugins) {
                foreach ((array) $plugins as $name) {
                    if (isset($inherited[$type][$name])) {
                        if ($this->isDebugClass($inherited[$type][$name]['instance'])) {
                            continue;
                        }
                        $this->data[$definitionTypes[$definitionType]][$type][] = [
                            'class'      => $inherited[$type][$name]['instance'],
                            'name'       => $name,
                            'sort_order' => $inherited[$type][$name]['sortOrder'],
                            'method'     => $definitionTypes[$definitionType] . ucfirst($method),
                        ];
                    }
                }
            }
        }

        return $this;
    }

    public function hasPlugins(): bool
    {
        return !empty($this->data[self::BEFORE])
            || !empty($this->data[self::AROUND])
            || !empty($this->data[self::AFTER]);
    }

    public function getBeforePlugins(): array
    {
        return $this->data[self::BEFORE] ?? [];
    }

    public function getAroundPlugins(): array
    {
        return $this->data[self::AROUND] ?? [];
    }

    public function getAfterPlugins(): array
    {
        return $this->data[self::AFTER] ?? [];
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

    public function isEnabled()
    {
        return $this->helper->isPluginDataCollectorEnabled();
    }

    private function isDebugClass($class)
    {
        return strpos($class, 'ClawRock\Debug') === 0;
    }
}
