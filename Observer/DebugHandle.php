<?php

namespace ClawRock\Debug\Observer;

use ClawRock\Debug\Model\Profiler;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class DebugHandle implements ObserverInterface
{
    /**
     * @var \ClawRock\Debug\Helper\Config
     */
    private $config;

    public function __construct(
        \ClawRock\Debug\Helper\Config $config
    ) {
        $this->config = $config;
    }

    public function execute(Observer $observer)
    {
        if ($this->config->isEnabled()) {
            $observer->getLayout()->getUpdate()->addHandle('clawrock_debug');
        }

        if ($observer->getFullActionName() === Profiler::TOOLBAR_FULL_ACTION_NAME) {
            $observer->getLayout()->getUpdate()->removeHandle('default');
        }
    }
}
