<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\Collector;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class ConfigCollector implements CollectorInterface
{
    public const NAME = 'config';
    public const STORE_ID = 'store_id';
    public const STORE_NAME = 'store_name';
    public const STORE_CODE = 'store_code';
    public const WEBSITE_ID = 'website_id';
    public const WEBSITE_NAME = 'website_name';
    public const WEBSITE_CODE = 'website_code';
    public const DEVELOPER_MODE = 'developer_mode';
    public const TOKEN = 'token';
    public const VERSION = 'version';
    public const MODULES = 'modules';
    public const XDEBUG_ENABLED = 'xdebug_enabled';
    public const EACCEL_ENABLED = 'eaccel_enabled';
    public const APC_ENABLED = 'apc_enabled';
    public const XCACHE_ENABLED = 'xcache_enabled';
    public const WINCACHE_ENABLED = 'wincache_enabled';
    public const OPCACHE_ENABLED = 'zend_opcache_enabled';

    private \Magento\Store\Model\StoreManagerInterface $storeManager;
    private \ClawRock\Debug\Helper\Config $config;
    private \ClawRock\Debug\Helper\Url $url;
    private \ClawRock\Debug\Model\DataCollector $dataCollector;
    private \ClawRock\Debug\Model\Info\MagentoInfo $magentoInfo;
    private \ClawRock\Debug\Model\Info\ExtensionInfo $extensionInfo;
    private \ClawRock\Debug\Model\Storage\HttpStorage $httpStorage;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \ClawRock\Debug\Helper\Config $config,
        \ClawRock\Debug\Helper\Url $url,
        \ClawRock\Debug\Model\DataCollectorFactory $dataCollectorFactory,
        \ClawRock\Debug\Model\Info\MagentoInfo $magentoInfo,
        \ClawRock\Debug\Model\Info\ExtensionInfo $extensionInfo,
        \ClawRock\Debug\Model\Storage\HttpStorage $httpStorage
    ) {
        $this->storeManager = $storeManager;
        $this->config = $config;
        $this->dataCollector = $dataCollectorFactory->create();
        $this->magentoInfo = $magentoInfo;
        $this->extensionInfo = $extensionInfo;
        $this->httpStorage = $httpStorage;
        $this->url = $url;
    }

    public function collect(): CollectorInterface
    {
        $tokenHeader = $this->httpStorage->getResponse()->getHeader('X-Debug-Token');
        $this->dataCollector->setData([
            self::STORE_ID => $this->storeManager->getStore()->getId(),
            self::STORE_NAME => $this->storeManager->getStore()->getName(),
            self::STORE_CODE => $this->storeManager->getStore()->getCode(),
            self::WEBSITE_ID => $this->storeManager->getWebsite()->getId(),
            self::WEBSITE_NAME => $this->storeManager->getWebsite()->getName(),
            self::WEBSITE_CODE => $this->storeManager->getWebsite()->getCode(),
            self::DEVELOPER_MODE => $this->magentoInfo->isDeveloperMode(),
            self::TOKEN => $tokenHeader instanceof \Laminas\Http\Header\HeaderInterface
                ? $tokenHeader->getFieldValue()
                : '',
            self::VERSION => $this->magentoInfo->getVersion(),
            self::MODULES => $this->magentoInfo->getModules(),
            self::XDEBUG_ENABLED => $this->extensionInfo->isXdebugEnabled(),
            self::EACCEL_ENABLED => $this->extensionInfo->isEAcceleratorEnabled(),
            self::APC_ENABLED => $this->extensionInfo->isApcEnabled(),
            self::XCACHE_ENABLED => $this->extensionInfo->isXCacheEnabled(),
            self::WINCACHE_ENABLED => $this->extensionInfo->isWinCacheEnabled(),
            self::OPCACHE_ENABLED => $this->extensionInfo->isZendOpcacheEnabled(),
        ]);

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->config->isConfigCollectorEnabled();
    }

    public function getVersion(): string
    {
        return $this->dataCollector->getData(self::VERSION) ?? '2';
    }

    public function getStoreId(): string
    {
        return $this->dataCollector->getData(self::STORE_ID) ?? '';
    }

    public function getStoreName(): string
    {
        return $this->dataCollector->getData(self::STORE_NAME) ?? '';
    }

    public function getStoreCode(): string
    {
        return $this->dataCollector->getData(self::STORE_CODE) ?? '';
    }

    public function getWebsiteId(): string
    {
        return $this->dataCollector->getData(self::WEBSITE_ID) ?? '';
    }

    public function getWebsiteName(): string
    {
        return $this->dataCollector->getData(self::WEBSITE_NAME) ?? '';
    }

    public function getWebsiteCode(): string
    {
        return $this->dataCollector->getData(self::WEBSITE_CODE) ?? '';
    }

    public function getToken(): string
    {
        return $this->dataCollector->getData(self::TOKEN) ?? '';
    }

    public function getProfilerUrl(): string
    {
        return $this->url->getProfilerUrl($this->getToken());
    }

    public function getAdminUrl(): string
    {
        return $this->url->getAdminUrl();
    }

    public function getConfigurationUrl(): string
    {
        return $this->url->getConfigurationUrl();
    }

    public function getPHPVersion(): string
    {
        return PHP_VERSION;
    }

    public function isDeveloperMode(): bool
    {
        return $this->dataCollector->getData(self::DEVELOPER_MODE) ?? false;
    }

    public function getModules(): array
    {
        return $this->dataCollector->getData(self::MODULES) ?? [];
    }

    public function hasXDebug(): bool
    {
        return $this->dataCollector->getData(self::XDEBUG_ENABLED) ?? false;
    }

    public function hasAccelerator(): bool
    {
        return $this->hasApc()
            || $this->hasZendOpcache()
            || $this->hasEAccelerator()
            || $this->hasXCache()
            || $this->hasWinCache();
    }

    public function hasApc(): bool
    {
        return $this->dataCollector->getData(self::APC_ENABLED) ?? false;
    }

    public function hasZendOpcache(): bool
    {
        return $this->dataCollector->getData(self::OPCACHE_ENABLED) ?? false;
    }

    public function hasEAccelerator(): bool
    {
        return $this->dataCollector->getData(self::EACCEL_ENABLED) ?? false;
    }

    public function hasXCache(): bool
    {
        return $this->dataCollector->getData(self::XCACHE_ENABLED) ?? false;
    }

    public function hasWinCache(): bool
    {
        return $this->dataCollector->getData(self::WINCACHE_ENABLED) ?? false;
    }

    public function getPHPSAPI(): string
    {
        return PHP_SAPI;
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
