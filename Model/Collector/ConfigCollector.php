<?php

namespace ClawRock\Debug\Model\Collector;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class ConfigCollector implements CollectorInterface
{
    const NAME = 'config';

    const STORE_ID             = 'store_id';
    const STORE_NAME           = 'store_name';
    const STORE_CODE           = 'store_code';
    const WEBSITE_ID           = 'website_id';
    const WEBSITE_NAME     = 'website_name';
    const WEBSITE_CODE     = 'website_code';
    const DEVELOPER_MODE   = 'developer_mode';
    const TOKEN            = 'token';
    const VERSION          = 'version';
    const MODULES          = 'modules';
    const XDEBUG_ENABLED   = 'xdebug_enabled';
    const EACCEL_ENABLED   = 'eaccel_enabled';
    const APC_ENABLED      = 'apc_enabled';
    const XCACHE_ENABLED   = 'xcache_enabled';
    const WINCACHE_ENABLED = 'wincache_enabled';
    const OPCACHE_ENABLED  = 'zend_opcache_enabled';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \ClawRock\Debug\Helper\Config
     */
    private $config;

    /**
     * @var \ClawRock\Debug\Helper\Url
     */
    private $url;

    /**
     * @var \ClawRock\Debug\Model\DataCollector
     */
    private $dataCollector;

    /**
     * @var \ClawRock\Debug\Model\Info\MagentoInfo
     */
    private $magentoInfo;

    /**
     * @var \ClawRock\Debug\Model\Info\ExtensionInfo
     */
    private $extensionInfo;

    /**
     * @var \ClawRock\Debug\Model\Storage\HttpStorage
     */
    private $httpStorage;

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
        $this->dataCollector->setData([
            self::STORE_ID         => $this->storeManager->getStore()->getId(),
            self::STORE_NAME       => $this->storeManager->getStore()->getName(),
            self::STORE_CODE       => $this->storeManager->getStore()->getCode(),
            self::WEBSITE_ID       => $this->storeManager->getWebsite()->getId(),
            self::WEBSITE_NAME     => $this->storeManager->getWebsite()->getName(),
            self::WEBSITE_CODE     => $this->storeManager->getWebsite()->getCode(),
            self::DEVELOPER_MODE   => $this->magentoInfo->isDeveloperMode(),
            self::TOKEN            => $this->httpStorage->getResponse()->getHeader('X-Debug-Token')->getFieldValue(),
            self::VERSION          => $this->magentoInfo->getVersion(),
            self::MODULES          => $this->magentoInfo->getModules(),
            self::XDEBUG_ENABLED   => $this->extensionInfo->isXdebugEnabled(),
            self::EACCEL_ENABLED   => $this->extensionInfo->isEAcceleratorEnabled(),
            self::APC_ENABLED      => $this->extensionInfo->isApcEnabled(),
            self::XCACHE_ENABLED   => $this->extensionInfo->isXCacheEnabled(),
            self::WINCACHE_ENABLED => $this->extensionInfo->isWinCacheEnabled(),
            self::OPCACHE_ENABLED  => $this->extensionInfo->isZendOpcacheEnabled(),
        ]);

        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->config->isConfigCollectorEnabled();
    }

    public function getVersion()
    {
        return $this->dataCollector->getData(self::VERSION) ?? '2';
    }

    public function getStoreId()
    {
        return $this->dataCollector->getData(self::STORE_ID) ?? '';
    }

    public function getStoreName()
    {
        return $this->dataCollector->getData(self::STORE_NAME) ?? '';
    }

    public function getStoreCode()
    {
        return $this->dataCollector->getData(self::STORE_CODE) ?? '';
    }

    public function getWebsiteId()
    {
        return $this->dataCollector->getData(self::WEBSITE_ID) ?? '';
    }

    public function getWebsiteName()
    {
        return $this->dataCollector->getData(self::WEBSITE_NAME) ?? '';
    }

    public function getWebsiteCode()
    {
        return $this->dataCollector->getData(self::WEBSITE_CODE) ?? '';
    }

    public function getToken()
    {
        return $this->dataCollector->getData(self::TOKEN) ?? '';
    }

    public function getProfilerUrl()
    {
        return $this->url->getProfilerUrl($this->getToken());
    }

    public function getAdminUrl()
    {
        return $this->url->getAdminUrl();
    }

    public function getConfigurationUrl()
    {
        return $this->url->getConfigurationUrl();
    }

    public function getPHPVersion()
    {
        return PHP_VERSION;
    }

    public function isDeveloperMode()
    {
        return $this->dataCollector->getData(self::DEVELOPER_MODE) ?? false;
    }

    public function getModules()
    {
        return $this->dataCollector->getData(self::MODULES) ?? [];
    }

    public function hasXDebug()
    {
        return $this->dataCollector->getData(self::XDEBUG_ENABLED) ?? false;
    }

    public function hasAccelerator()
    {
        return $this->hasApc()
            || $this->hasZendOpcache()
            || $this->hasEAccelerator()
            || $this->hasXCache()
            || $this->hasWinCache();
    }

    public function hasApc()
    {
        return $this->dataCollector->getData(self::APC_ENABLED) ?? false;
    }

    public function hasZendOpcache()
    {
        return $this->dataCollector->getData(self::OPCACHE_ENABLED) ?? false;
    }

    public function hasEAccelerator()
    {
        return $this->dataCollector->getData(self::EACCEL_ENABLED) ?? false;
    }

    public function hasXCache()
    {
        return $this->dataCollector->getData(self::XCACHE_ENABLED) ?? false;
    }

    public function hasWinCache()
    {
        return $this->dataCollector->getData(self::WINCACHE_ENABLED) ?? false;
    }

    public function getPHPSAPI()
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
