<?php

namespace ClawRock\Debug\Model\Info;

use Magento\Framework\App\State;

class MagentoInfo
{
    const MODULES_CACHE_ID = 'ClawRock_Debug::modules';
    const VERSION_CACHE_ID = 'ClawRock_Debug::version';

    /**
     * @var \Magento\Framework\App\State
     */
    private $appState;

    /**
     * @var \Magento\Framework\App\Cache\Type\Config
     */
    private $cache;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    private $moduleList;

    public function __construct(
        \Magento\Framework\App\State $appState,
        \Magento\Framework\App\Cache\Type\Config $cache,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\Module\ModuleListInterface $moduleList
    ) {
        $this->appState = $appState;
        $this->cache = $cache;
        $this->productMetadata = $productMetadata;
        $this->moduleList = $moduleList;
    }

    public function isDeveloperMode(): bool
    {
        return $this->appState->getMode() === State::MODE_DEVELOPER;
    }

    public function getVersion(): string
    {
        if ($this->cache->test(self::VERSION_CACHE_ID)) {
            return $this->cache->load(self::VERSION_CACHE_ID);
        }

        $version = $this->productMetadata->getVersion() . ' ' . $this->productMetadata->getEdition();
        $this->cache->save($version, self::VERSION_CACHE_ID);

        return $version;
    }

    public function getModules(): array
    {
        if ($this->cache->test(self::MODULES_CACHE_ID)) {
            return unserialize($this->cache->load(self::MODULES_CACHE_ID));
        }

        $modules = $this->moduleList->getAll();
        $this->cache->save(serialize($modules), self::MODULES_CACHE_ID);

        return $modules;
    }
}
