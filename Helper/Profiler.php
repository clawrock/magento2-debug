<?php

namespace ClawRock\Debug\Helper;

use ClawRock\Debug\Model\Config\Source\ErrorHandler;
use ClawRock\Debug\Model\DataCollector\DataCollectorInterface;
use ClawRock\Debug\Model\Profiler as ProfilerModel;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\HTTP\PhpEnvironment\Response;

class Profiler extends AbstractHelper
{
    const CONFIG_ENABLED                    = 'clawrock_debug/general/active';
    const CONFIG_ALLOWED_IPS                = 'clawrock_debug/general/allowed_ips';
    const CONFIG_ERROR_HANDLER              = 'clawrock_debug/general/error_handler';
    const CONFIG_PERFORMANCE_COLOR          = 'clawrock_debug/performance/%s_color';
    const CONFIG_DATA_COLLECTOR_AJAX        = 'clawrock_debug/collector/ajax';
    const CONFIG_DATA_COLLECTOR_CACHE       = 'clawrock_debug/collector/cache';
    const CONFIG_DATA_COLLECTOR_CONFIG      = 'clawrock_debug/collector/config';
    const CONFIG_DATA_COLLECTOR_CUSTOMER    = 'clawrock_debug/collector/customer';
    const CONFIG_DATA_COLLECTOR_DATABASE    = 'clawrock_debug/collector/database';
    const CONFIG_DATA_COLLECTOR_EVENT       = 'clawrock_debug/collector/event';
    const CONFIG_DATA_COLLECTOR_LAYOUT      = 'clawrock_debug/collector/layout';
    const CONFIG_DATA_COLLECTOR_MEMORY      = 'clawrock_debug/collector/memory';
    const CONFIG_DATA_COLLECTOR_MODEL       = 'clawrock_debug/collector/model';
    const CONFIG_DATA_COLLECTOR_REQUEST     = 'clawrock_debug/collector/request';
    const CONFIG_DATA_COLLECTOR_TIME        = 'clawrock_debug/collector/time';
    const CONFIG_DATA_COLLECTOR_TRANSLATION = 'clawrock_debug/collector/translation';

    const DATA_COLLECTORS = 'clawrock_debug/profiler/collectors';

    /**
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $backendUrl;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\State $appState,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Model\UrlInterface $backendUrl
    ) {
        parent::__construct($context);
        $this->appState = $appState;
        $this->registry = $registry;
        $this->backendUrl = $backendUrl;
    }

    public function getCollectorUrl($token, DataCollectorInterface $collector)
    {
        return $this->getUrl($token, $collector->getCollectorName());
    }

    public function getUrl($token = null, $panel = null)
    {
        $params = [];
        if ($token) {
            $params[ProfilerModel::URL_TOKEN_PARAMETER] = $token;
        }
        if ($panel) {
            $params['panel'] = $panel;
        }

        return $this->_getUrl('_debug/profiler/info', $params);
    }

    public function getTokenFromResponse(Response $response)
    {
        $token = null;
        /** @var \Zend\Http\Header\HeaderInterface $header */
        foreach ($response->getHeaders() as $header) {
            if ($header->getFieldName() === 'X-Debug-Token') {
                $token = $header->getFieldValue();
                break;
            }
        }

        return $token;
    }

    public function getConfigurationUrl()
    {
        return $this->backendUrl->getUrl('admin/system_config/edit/section/clawrock_debug');
    }

    public function isWhoopsEnabled()
    {
        return $this->getErrorHandler() === ErrorHandler::WHOOPS;
    }

    public function getErrorHandler()
    {
        if (!$this->isEnabled()) {
            return ErrorHandler::MAGENTO;
        }

        return $this->scopeConfig->getValue(self::CONFIG_ERROR_HANDLER, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }

    public function isEnabled()
    {
        return $this->scopeConfig->getValue(self::CONFIG_ENABLED, ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
            && $this->appState->getMode() === \Magento\Framework\App\State::MODE_DEVELOPER;
    }

    public function getAllowedIPs()
    {
        return array_map('trim', explode(',', $this->scopeConfig->getValue(
            self::CONFIG_ALLOWED_IPS,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        )));
    }

    public function isAllowedIP()
    {
        return in_array($_SERVER['REMOTE_ADDR'], $this->getAllowedIPs());
    }

    public function getDataCollectors()
    {
        return $this->scopeConfig->getValue(self::DATA_COLLECTORS, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }

    public function isAjaxDataCollectorEnabled()
    {
        return (bool) $this->scopeConfig->getValue(
            self::CONFIG_DATA_COLLECTOR_AJAX,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function isCacheDataCollectorEnabled()
    {
        return (bool) $this->scopeConfig->getValue(
            self::CONFIG_DATA_COLLECTOR_CACHE,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function isConfigDataCollectorEnabled()
    {
        return (bool) $this->scopeConfig->getValue(
            self::CONFIG_DATA_COLLECTOR_CONFIG,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function isCustomerDataCollectorEnabled()
    {
        return (bool) $this->scopeConfig->getValue(
            self::CONFIG_DATA_COLLECTOR_CUSTOMER,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function isDatabaseDataCollectorEnabled()
    {
        return (bool) $this->scopeConfig->getValue(
            self::CONFIG_DATA_COLLECTOR_DATABASE,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function isEventDataCollectorEnabled()
    {
        return (bool) $this->scopeConfig->getValue(
            self::CONFIG_DATA_COLLECTOR_EVENT,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function isLayoutDataCollectorEnabled()
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_DATA_COLLECTOR_LAYOUT,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        ) && !$this->isFPCRequest();
    }

    public function isFPCRequest(): bool
    {
        return (bool) $this->registry->registry('debug_fpc_request');
    }

    public function isMemoryDataCollectorEnabled()
    {
        return (bool) $this->scopeConfig->getValue(
            self::CONFIG_DATA_COLLECTOR_MEMORY,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function isModelDataCollectorEnabled()
    {
        return (bool) $this->scopeConfig->getValue(
            self::CONFIG_DATA_COLLECTOR_MODEL,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function isRequestDataCollectorEnabled()
    {
        return (bool) $this->scopeConfig->getValue(
            self::CONFIG_DATA_COLLECTOR_REQUEST,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function isTimeDataCollectorEnabled()
    {
        return (bool) $this->scopeConfig->getValue(
            self::CONFIG_DATA_COLLECTOR_TIME,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function isTranslationDataCollectorEnabled()
    {
        return (bool) $this->scopeConfig->getValue(
            self::CONFIG_DATA_COLLECTOR_TRANSLATION,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        ) && !$this->isFPCRequest();
    }

    public function getPerformanceColor($event)
    {
        return $this->scopeConfig->getValue(
            sprintf(self::CONFIG_PERFORMANCE_COLOR, $event),
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }
}
