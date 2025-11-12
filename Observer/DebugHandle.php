<?php
declare(strict_types=1);

namespace ClawRock\Debug\Observer;

use ClawRock\Debug\Model\Profiler;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class DebugHandle implements ObserverInterface
{
    public function __construct(
        private \ClawRock\Debug\Helper\Config $config
    ) {
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
