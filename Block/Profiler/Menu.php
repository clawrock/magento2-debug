<?php

namespace ClawRock\Debug\Block\Profiler;

use Magento\Framework\View\Element\Template;

class Menu extends Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;
    /**
     * @var \ClawRock\Debug\Helper\Profiler
     */
    private $helper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \ClawRock\Debug\Helper\Profiler $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->helper = $helper;
    }

    public function getProfile()
    {
        return $this->registry->registry('current_profile');
    }

    public function getProfilerUrl($token = null, $panel = null)
    {
        return $this->helper->getUrl($token, $panel);
    }

    public function getCollector($name)
    {
        return $this->getProfile()->getCollector($name);
    }

    public function getToken()
    {
        return $this->getProfile()->getToken();
    }

    public function getPanel()
    {
        return $this->registry->registry('current_panel');
    }

    public function getMenuBlocks()
    {
        if (!$this->getProfile()) {
            return [];
        }

        $blocks = [];

        /** @var \ClawRock\Debug\Model\DataCollector\DataCollectorInterface[] $collectors */
        $collectors = $this->getProfile()->getCollectors();
        foreach ($this->getChildNames() as $name) {
            /** @var \ClawRock\Debug\Block\Profiler\Collector $block */
            $block = $this->_layout->getBlock($name);

            if (!isset($collectors[$name])) {
                continue;
            }

            $block->setCollector($collectors[$name]);
            $blocks[$collectors[$name]->getCollectorName()] = $block;
        }

        return $blocks;
    }

    public function getConfigurationUrl()
    {
        return $this->helper->getConfigurationUrl();
    }
}
