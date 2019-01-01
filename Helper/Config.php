<?php

namespace ClawRock\Debug\Helper;

use ClawRock\Debug\Exception\CollectorNotFoundException;
use ClawRock\Debug\Model\Config\Source\ErrorHandler;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class Config
{
    const CONFIG_ENABLED               = 'clawrock_debug/general/active';
    const CONFIG_ENABLED_ADMINHTML     = 'clawrock_debug/general/active_adminhtml';
    const CONFIG_ALLOWED_IPS           = 'clawrock_debug/general/allowed_ips';
    const CONFIG_ERROR_HANDLER         = 'clawrock_debug/general/error_handler';
    const CONFIG_TIME_PRECISION        = 'clawrock_debug/time/precision';
    const CONFIG_PERFORMANCE_COLOR     = 'clawrock_debug/performance/%s_color';
    const CONFIG_COLLECTOR_AJAX        = 'clawrock_debug/collector/ajax';
    const CONFIG_COLLECTOR_CACHE       = 'clawrock_debug/collector/cache';
    const CONFIG_COLLECTOR_CONFIG      = 'clawrock_debug/collector/config';
    const CONFIG_COLLECTOR_CUSTOMER    = 'clawrock_debug/collector/customer';
    const CONFIG_COLLECTOR_DATABASE    = 'clawrock_debug/collector/database';
    const CONFIG_COLLECTOR_EVENT       = 'clawrock_debug/collector/event';
    const CONFIG_COLLECTOR_PLUGIN      = 'clawrock_debug/collector/plugin';
    const CONFIG_COLLECTOR_LAYOUT      = 'clawrock_debug/collector/layout';
    const CONFIG_COLLECTOR_MEMORY      = 'clawrock_debug/collector/memory';
    const CONFIG_COLLECTOR_MODEL       = 'clawrock_debug/collector/model';
    const CONFIG_COLLECTOR_TIME        = 'clawrock_debug/collector/time';
    const CONFIG_COLLECTOR_TRANSLATION = 'clawrock_debug/collector/translation';

    const COLLECTORS = 'clawrock_debug/profiler/collectors';

    /**
     * @var \Magento\Framework\PhraseFactory
     */
    private $phraseFactory;

    /**
     * @var \Magento\Framework\App\State
     */
    private $appState;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \ClawRock\Debug\Model\Storage\HttpStorage
     */
    private $httpStorage;

    public function __construct(
        \Magento\Framework\PhraseFactory $phraseFactory,
        \Magento\Framework\App\State $appState,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \ClawRock\Debug\Model\Storage\HttpStorage $httpStorage
    ) {
        $this->phraseFactory = $phraseFactory;
        $this->appState = $appState;
        $this->scopeConfig = $scopeConfig;
        $this->httpStorage = $httpStorage;
    }

    public function getErrorHandler(): string
    {
        if (!$this->isEnabled()) {
            return ErrorHandler::MAGENTO;
        }

        return $this->scopeConfig->getValue(self::CONFIG_ERROR_HANDLER, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }

    public function isEnabled(): bool
    {
        if ($this->appState->getMode() !== \Magento\Framework\App\State::MODE_DEVELOPER) {
            return false;
        }

        if (!$this->isActive()) {
            return false;
        }

        if ($this->appState->getAreaCode() === Area::AREA_ADMINHTML && !$this->isAdminhtmlActive()) {
            return false;
        }

        return true;
    }

    public function isActive(): bool
    {
        return $this->scopeConfig->getValue(self::CONFIG_ENABLED, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }

    public function isAdminhtmlActive(): bool
    {
        return $this->scopeConfig->getValue(self::CONFIG_ENABLED_ADMINHTML, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isFrontend(): bool
    {
        return $this->appState->getAreaCode() === Area::AREA_FRONTEND;
    }

    public function getAllowedIPs(): array
    {
        return array_map('trim', explode(',', $this->scopeConfig->getValue(
            self::CONFIG_ALLOWED_IPS,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        )));
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     * @return bool
     */
    public function isAllowedIP(): bool
    {
        return in_array($_SERVER['REMOTE_ADDR'], $this->getAllowedIPs());
    }

    public function getTimePrecision(): int
    {
        return (int) $this->scopeConfig->getValue(
            self::CONFIG_TIME_PRECISION,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function getCollectors(): array
    {
        return $this->scopeConfig->getValue(self::COLLECTORS, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }

    /**
     * @param string $name
     * @return string
     * @throws \ClawRock\Debug\Exception\CollectorNotFoundException
     */
    public function getCollectorClass(string $name): string
    {
        if (!isset($this->getCollectors()[$name])) {
            throw new CollectorNotFoundException($this->phraseFactory->create([
                'text' => 'Collector "%1" not found',
                'arguments' => $name
            ]));
        }

        return $this->getCollectors()[$name];
    }

    public function isAjaxCollectorEnabled(): bool
    {
        return (bool) $this->scopeConfig->getValue(
            self::CONFIG_COLLECTOR_AJAX,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function isCacheCollectorEnabled(): bool
    {
        return (bool) $this->scopeConfig->getValue(
            self::CONFIG_COLLECTOR_CACHE,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function isConfigCollectorEnabled(): bool
    {
        return (bool) $this->scopeConfig->getValue(
            self::CONFIG_COLLECTOR_CONFIG,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function isCustomerCollectorEnabled(): bool
    {
        return (bool) $this->scopeConfig->getValue(
            self::CONFIG_COLLECTOR_CUSTOMER,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function isDatabaseCollectorEnabled(): bool
    {
        return (bool) $this->scopeConfig->getValue(
            self::CONFIG_COLLECTOR_DATABASE,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function isEventCollectorEnabled(): bool
    {
        return (bool) $this->scopeConfig->getValue(
            self::CONFIG_COLLECTOR_EVENT,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function isPluginCollectorEnabled(): bool
    {
        return (bool) $this->scopeConfig->getValue(
            self::CONFIG_COLLECTOR_PLUGIN,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function isLayoutCollectorEnabled(): bool
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_COLLECTOR_LAYOUT,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        ) && !$this->httpStorage->isFPCRequest();
    }

    public function isMemoryCollectorEnabled(): bool
    {
        return (bool) $this->scopeConfig->getValue(
            self::CONFIG_COLLECTOR_MEMORY,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function isModelCollectorEnabled(): bool
    {
        return (bool) $this->scopeConfig->getValue(
            self::CONFIG_COLLECTOR_MODEL,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function isTimeCollectorEnabled(): bool
    {
        return (bool) $this->scopeConfig->getValue(
            self::CONFIG_COLLECTOR_TIME,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function isTranslationCollectorEnabled(): bool
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_COLLECTOR_TRANSLATION,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        ) && !$this->httpStorage->isFPCRequest();
    }

    public function getPerformanceColor(string $event): string
    {
        return $this->scopeConfig->getValue(
            sprintf(self::CONFIG_PERFORMANCE_COLOR, $event),
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }
}
