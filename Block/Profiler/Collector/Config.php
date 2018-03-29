<?php

namespace ClawRock\Debug\Block\Profiler\Collector;

use ClawRock\Debug\Block\Profiler\Collector;
use Magento\Framework\App\Area;

class Config extends Collector
{
    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $backendUrl;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \ClawRock\Debug\Helper\Profiler $helper,
        \ClawRock\Debug\Block\Profiler\Renderer\TableRenderer $tableRenderer,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        array $data = []
    ) {
        parent::__construct($context, $registry, $helper, $tableRenderer, $data);
        $this->backendUrl = $backendUrl;
    }

    public function getAdminUrl()
    {
        return $this->backendUrl->getRouteUrl(Area::AREA_ADMINHTML);
    }

    public function getConfigurationUrl()
    {
        return $this->backendUrl->getUrl('system_config/edit/section/clawrock_debug');
    }
}
