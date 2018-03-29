<?php

namespace ClawRock\Debug\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class AllowedIP implements ObserverInterface
{
    /**
     * @var \ClawRock\Debug\Helper\Profiler
     */
    protected $profilerHelper;

    public function __construct(
        \ClawRock\Debug\Helper\Profiler $profilerHelper
    ) {
        $this->profilerHelper = $profilerHelper;
    }

    public function execute(Observer $observer)
    {
        if ($this->profilerHelper->isAllowedIP()) {
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
