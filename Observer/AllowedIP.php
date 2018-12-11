<?php

namespace ClawRock\Debug\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class AllowedIP implements ObserverInterface
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
        if ($this->config->isAllowedIP()) {
            return;
        }

        /** @var \Magento\Framework\App\Request\Http $request */
        $request = $observer->getRequest();
        $request->initForward();
        $request->setControllerName('noroute');
        $request->setModuleName('cms');
        $request->setActionName('index');
        $request->setDispatched(false);
    }
}
