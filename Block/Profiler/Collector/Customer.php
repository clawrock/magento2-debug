<?php

namespace ClawRock\Debug\Block\Profiler\Collector;

use ClawRock\Debug\Block\Profiler\Collector;

class Customer extends Collector
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \ClawRock\Debug\Helper\Profiler $helper,
        \ClawRock\Debug\Block\Profiler\Renderer\TableRenderer $tableRenderer,
        \Magento\Framework\UrlInterface $url,
        array $data = []
    ) {
        parent::__construct($context, $registry, $helper, $tableRenderer, $data);
        $this->url = $url;
    }

    public function getLoginUrl()
    {
        return $this->url->getUrl('customer/account/login');
    }

    public function getLogoutUrl()
    {
        return $this->url->getUrl('customer/account/logout');
    }

    public function getRegisterUrl()
    {
        return $this->url->getUrl('customer/account/create');
    }
}
