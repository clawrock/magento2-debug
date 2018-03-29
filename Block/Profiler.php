<?php

namespace ClawRock\Debug\Block;

use ClawRock\Debug\Model\DataCollector\DataCollectorInterface;
use Magento\Framework\View\Element\Template;

class Profiler extends Template
{
    /**
     * @var \ClawRock\Debug\Model\Profile
     */
    protected $profile;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \ClawRock\Debug\Helper\Profiler
     */
    protected $helper;

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
        if ($this->profile === null) {
            $this->profile = $this->registry->registry('current_profile');
        }

        return $this->profile;
    }

    public function getToken()
    {
        return $this->getProfile()->getToken();
    }

    public function getProfilerUrl($token = null, $panel = null)
    {
        return $this->helper->getUrl($token, $panel);
    }

    public function getCollectorUrl($token, DataCollectorInterface $collector)
    {
        return $this->helper->getCollectorUrl($token, $collector);
    }
}
