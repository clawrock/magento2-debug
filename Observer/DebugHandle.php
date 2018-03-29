<?php

namespace ClawRock\Debug\Observer;

use ClawRock\Debug\Model\Profiler;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class DebugHandle implements ObserverInterface
{
    /**
     * @var \ClawRock\Debug\Helper\Profiler
     */
    protected $helper;

    public function __construct(
        \ClawRock\Debug\Helper\Profiler $helper
    ) {
        $this->helper = $helper;
    }

    public function execute(Observer $observer)
    {
        if ($this->helper->isEnabled()) {
            $observer->getLayout()->getUpdate()->addHandle('clawrock_debug');
        }

        if ($observer->getFullActionName() === Profiler::TOOLBAR_FULL_ACTION_NAME) {
            $observer->getLayout()->getUpdate()->removeHandle('default');
        }
    }
}
