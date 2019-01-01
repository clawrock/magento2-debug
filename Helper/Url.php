<?php

namespace ClawRock\Debug\Helper;

use ClawRock\Debug\Model\Profiler;
use Magento\Framework\App\Area;
use Magento\Framework\HTTP\PhpEnvironment\Request;

class Url
{
    const CONFIGURATION_URL_PATH = 'debug/profiler/config';

    const PROFILER_URL_PATH = '_debug/profiler/info';

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $url;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    private $backendUrl;

    /**
     * @var \Magento\Framework\App\Route\ConfigInterface\Proxy
     */
    private $routeConfigProxy;

    /**
     * @var \Magento\Framework\App\DefaultPathInterface
     */
    private $defaultPath;

    public function __construct(
        \Magento\Framework\Url $url,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Framework\App\Route\ConfigInterface\Proxy $routeConfigProxy,
        \Magento\Framework\App\DefaultPathInterface $defaultPath
    ) {
        $this->url = $url;
        $this->backendUrl = $backendUrl;
        $this->routeConfigProxy = $routeConfigProxy;
        $this->defaultPath = $defaultPath;
    }

    public function getAdminUrl()
    {
        return $this->backendUrl->getRouteUrl(Area::AREA_ADMINHTML);
    }

    public function getConfigurationUrl()
    {
        return $this->backendUrl->getUrl(self::CONFIGURATION_URL_PATH);
    }

    public function getProfilerUrl($token = null, $panel = null)
    {
        $params = [];
        if ($token) {
            $params[Profiler::URL_TOKEN_PARAMETER] = $token;
        }
        if ($panel) {
            $params[Profiler::URL_PANEL_PARAMETER] = $panel;
        }

        return $this->url->getUrl(self::PROFILER_URL_PATH, $params);
    }

    public function getToolbarUrl(string $token): string
    {
        return $this->url->getUrl('_debug/profiler/toolbar', [
            Profiler::URL_TOKEN_PARAMETER => $token,
            '_nosid' => true
        ]);
    }

    public function getRequestFullActionName(Request $request): string
    {
        try {
            return $request->getRouteName() . '_' . $request->getControllerName() . '_' . $request->getActionName();
        } catch (\Exception $e) {
            return 'unknown';
        }
    }
}
