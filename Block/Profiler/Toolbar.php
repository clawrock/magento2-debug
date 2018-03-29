<?php

namespace ClawRock\Debug\Block\Profiler;

use ClawRock\Debug\Block\Profiler as ProfilerBlock;
use ClawRock\Debug\Model\DataCollector\DataCollectorInterface;
use ClawRock\Debug\Model\Profiler;
use Magento\Framework\App\Area;

class Toolbar extends ProfilerBlock
{
    /**
     * @var \ClawRock\Debug\Model\DataCollector\DataCollectorInterface[]
     */
    protected $collectors;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layout;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $backendUrl;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \ClawRock\Debug\Helper\Profiler $helper,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Framework\UrlInterface $url,
        array $data = []
    ) {
        parent::__construct($context, $registry, $helper, $data);
        $this->layout = $layout;
        $this->backendUrl = $backendUrl;
        $this->url = $url;
    }

    public function getToken()
    {
        if ($this->getData('token')) {
            return $this->getData('token');
        }

        if ($this->getProfile()) {
            return $this->getProfile()->getToken();
        }

        return false;
    }

    public function getCollectors()
    {
        if ($this->collectors === null) {
            $this->collectors = $this->getProfile()->getCollectors();
        }

        return $this->collectors;
    }

    public function getCollectorBlocks()
    {
        $blocks = [];

        foreach ($this->getCollectors() as $collector) {
            /** @var DataCollectorInterface $collector */
            if (!$block = $this->layout->getBlock($collector->getBlockName())) {
                continue;
            }
            /** @var \ClawRock\Debug\Block\Profiler\Collector $block */
            $block->setCollector($collector);
            $block->setData('token', $this->getToken());
            $blocks[$collector->getCollectorName()] = $block;
        }

        return $blocks;
    }

    public function getUrl($route = '', $params = [])
    {
        return $this->url->getUrl($route, $params);
    }

    public function getToolbarUrl()
    {
        return $this->getUrl('_debug/profiler/toolbar', [
            Profiler::URL_TOKEN_PARAMETER => $this->getToken(),
            '_nosid' => true
        ]);
    }

    public function getAdminUrl()
    {
        return $this->backendUrl->getRouteUrl(Area::AREA_ADMINHTML);
    }
}
