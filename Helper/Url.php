<?php
declare(strict_types=1);

namespace ClawRock\Debug\Helper;

use ClawRock\Debug\Model\Profiler;
use Magento\Framework\App\Area;
use Magento\Framework\HTTP\PhpEnvironment\Request;

class Url
{
    public const CONFIGURATION_URL_PATH = 'debug/profiler/config';
    public const PROFILER_URL_PATH = '_debug/profiler/info';

    private \Magento\Framework\UrlInterface $url;
    private \Magento\Backend\Model\UrlInterface $backendUrl;

    public function __construct(
        \Magento\Framework\Url $url,
        \Magento\Backend\Model\UrlInterface $backendUrl
    ) {
        $this->url = $url;
        $this->backendUrl = $backendUrl;
    }

    public function getAdminUrl(): string
    {
        return $this->backendUrl->getRouteUrl(Area::AREA_ADMINHTML);
    }

    public function getConfigurationUrl(): string
    {
        return $this->backendUrl->getUrl(self::CONFIGURATION_URL_PATH);
    }

    public function getProfilerUrl(?string $token = null, ?string $panel = null): string
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
            '_nosid' => true,
        ]);
    }

    public function getRequestFullActionName(Request $request): string
    {
        try {
            // @phpstan-ignore-next-line
            return $request->getRouteName() . '_' . $request->getControllerName() . '_' . $request->getActionName();
        } catch (\Exception $e) {
            return 'unknown';
        }
    }
}
