<?php

namespace ClawRock\Debug\Model\DataCollector;

class ConfigDataCollector extends AbstractDataCollector
{
    const NAME             = 'config';
    const MODULES_CACHE_ID = 'ClawRock_Debug::modules';
    const VERSION_CACHE_ID = 'ClawRock_Debug::version';

    const STORE_ID             = 'store_id';
    const STORE_NAME           = 'store_name';
    const STORE_CODE           = 'store_code';
    const WEBSITE_ID           = 'website_id';
    const WEBSITE_NAME         = 'website_name';
    const WEBSITE_CODE         = 'website_code';
    const DEVELOPER_MODE       = 'developer_mode';
    const TOKEN                = 'token';
    const VERSION              = 'version';
    const MODULES              = 'modules';
    const XDEBUG_ENABLED       = 'xdebug_enabled';
    const EACCEL_ENABLED       = 'eaccel_enabled';
    const APC_ENABLED          = 'apc_enabled';
    const XCACHE_ENABLED       = 'xcache_enabled';
    const WINCACHE_ENABLED     = 'wincache_enabled';
    const ZEND_OPCACHE_ENABLED = 'zend_opcache_enabled';

    /**
     * @var \Magento\Framework\App\Cache\Type\Config
     */
    private $cache;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var \Magento\Framework\App\State
     */
    private $state;

    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    private $moduleList;

    /**
     * @var \ClawRock\Debug\Helper\Profiler
     */
    private $token;

    public function __construct(
        \ClawRock\Debug\Helper\Profiler $helper,
        \Magento\Framework\App\Cache\Type\Config $cache,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\App\State $state,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \ClawRock\Debug\Helper\Profiler $token
    ) {
        parent::__construct($helper);

        $this->cache = $cache;
        $this->storeManager = $storeManager;
        $this->productMetadata = $productMetadata;
        $this->state = $state;
        $this->moduleList = $moduleList;
        $this->token = $token;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param \Magento\Framework\App\Request\Http  $request
     * @param \Magento\Framework\App\Response\Http $response
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return $this
     */
    public function collect(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\Response\Http $response
    ) {
        $store = $this->storeManager->getStore();
        $website = $this->storeManager->getWebsite();

        $this->data = [
            self::STORE_ID             => $store->getId(),
            self::STORE_NAME           => $store->getName(),
            self::STORE_CODE           => $store->getCode(),
            self::WEBSITE_ID           => $website->getId(),
            self::WEBSITE_NAME         => $website->getName(),
            self::WEBSITE_CODE         => $website->getCode(),
            self::DEVELOPER_MODE       => $this->state->getMode() === $this->state::MODE_DEVELOPER,
            self::TOKEN                => $this->token->getTokenFromResponse($response),
            self::VERSION              => $this->collectVersion(),
            self::MODULES              => $this->collectModules(),
            self::XDEBUG_ENABLED       => extension_loaded('xdebug'),
            self::EACCEL_ENABLED       => extension_loaded('eaccelerator') && ini_get('eaccelerator.enable'),
            self::APC_ENABLED          => extension_loaded('apc') && ini_get('apc.enabled'),
            self::XCACHE_ENABLED       => extension_loaded('xcache') && ini_get('xcache.cacher'),
            self::WINCACHE_ENABLED     => extension_loaded('wincache') && ini_get('wincache.ocenabled'),
            self::ZEND_OPCACHE_ENABLED => extension_loaded('Zend OPcache') && ini_get('opcache.enable'),
        ];

        return $this;
    }

    protected function collectVersion()
    {
        if ($this->cache->test(self::VERSION_CACHE_ID)) {
            return unserialize($this->cache->load(self::VERSION_CACHE_ID));
        }

        $version = $this->productMetadata->getVersion() . ' ' . $this->productMetadata->getEdition();

        $this->cache->save(serialize($version), self::VERSION_CACHE_ID);

        return $version;
    }

    protected function collectModules()
    {
        if ($this->cache->test(self::MODULES_CACHE_ID)) {
            return unserialize($this->cache->load(self::MODULES_CACHE_ID));
        }

        $modules = $this->moduleList->getAll();

        $this->cache->save(serialize($modules), self::MODULES_CACHE_ID);

        return $modules;
    }

    public function getVersion()
    {
        return $this->data[self::VERSION] ?? '2';
    }

    public function getStoreId()
    {
        return $this->data[self::STORE_ID] ?? '';
    }

    public function getStoreName()
    {
        return $this->data[self::STORE_NAME] ?? '';
    }

    public function getStoreCode()
    {
        return $this->data[self::STORE_CODE] ?? '';
    }

    public function getWebsiteId()
    {
        return $this->data[self::WEBSITE_ID] ?? '';
    }

    public function getWebsiteName()
    {
        return $this->data[self::WEBSITE_NAME] ?? '';
    }

    public function getWebsiteCode()
    {
        return $this->data[self::WEBSITE_CODE] ?? '';
    }

    public function getToken()
    {
        return $this->data[self::TOKEN] ?? '';
    }

    public function getPHPVersion()
    {
        return PHP_VERSION;
    }

    public function isDeveloperMode()
    {
        return $this->data[self::DEVELOPER_MODE] ?? false;
    }

    public function getModules()
    {
        return $this->data[self::MODULES] ?? [];
    }

    public function hasXDebug()
    {
        return $this->data[self::XDEBUG_ENABLED] ?? false;
    }

    public function hasAccelerator()
    {
        return $this->hasApc() || $this->hasZendOpcache() || $this->hasEAccelerator() || $this->hasXCache() || $this->hasWinCache();
    }

    public function hasApc()
    {
        return $this->data[self::APC_ENABLED] ?? false;
    }

    public function hasZendOpcache()
    {
        return $this->data[self::ZEND_OPCACHE_ENABLED] ?? false;
    }

    public function hasEAccelerator()
    {
        return $this->data[self::EACCEL_ENABLED] ?? false;
    }

    public function hasXCache()
    {
        return $this->data[self::XCACHE_ENABLED] ?? false;
    }

    public function hasWinCache()
    {
        return $this->data[self::WINCACHE_ENABLED] ?? false;
    }

    public function getPHPSAPI()
    {
        return PHP_SAPI;
    }

    public function isEnabled()
    {
        return $this->helper->isCacheDataCollectorEnabled();
    }
}
