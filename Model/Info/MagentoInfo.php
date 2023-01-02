<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\Info;

use Magento\Framework\App\State;

class MagentoInfo
{
    const MODULES_CACHE_ID = 'ClawRock_Debug::modules';
    const VERSION_CACHE_ID = 'ClawRock_Debug::version';

    private \Magento\Framework\App\State $appState;
    private \Magento\Framework\App\Cache\Type\Config $cache;
    private \Magento\Framework\App\ProductMetadataInterface $productMetadata;
    private \Magento\Framework\Module\ModuleListInterface $moduleList;
    private \Magento\Framework\Serialize\SerializerInterface $serializer;

    public function __construct(
        \Magento\Framework\App\State $appState,
        \Magento\Framework\App\Cache\Type\Config $cache,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\Serialize\SerializerInterface $serializer
    ) {
        $this->appState = $appState;
        $this->cache = $cache;
        $this->productMetadata = $productMetadata;
        $this->moduleList = $moduleList;
        $this->serializer = $serializer;
    }

    public function isDeveloperMode(): bool
    {
        return $this->appState->getMode() === State::MODE_DEVELOPER;
    }

    public function getVersion(): string
    {
        if ($this->cache->test(self::VERSION_CACHE_ID)) {
            return (string) $this->cache->load(self::VERSION_CACHE_ID);
        }

        $version = $this->productMetadata->getVersion() . ' ' . $this->productMetadata->getEdition();
        $this->cache->save($version, self::VERSION_CACHE_ID);

        return $version;
    }

    public function getModules(): array
    {
        if ($this->cache->test(self::MODULES_CACHE_ID)) {
            $modules = $this->serializer->unserialize((string) $this->cache->load(self::MODULES_CACHE_ID));

            return is_array($modules) ? $modules : [];
        }

        $modules = $this->moduleList->getAll();
        $this->cache->save((string) $this->serializer->serialize($modules), self::MODULES_CACHE_ID);

        return $modules;
    }
}
