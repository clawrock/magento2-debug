<?php

namespace ClawRock\Debug\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class BeforeSendResponse implements ObserverInterface
{
    /**
     * @var \ClawRock\Debug\Model\Profiler
     */
    private $profiler;

    /**
     * @var \ClawRock\Debug\Helper\Profiler
     */
    private $profilerHelper;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    public function __construct(
        \ClawRock\Debug\Model\Profiler $profiler,
        \ClawRock\Debug\Helper\Profiler $profilerHelper,
        \Magento\Framework\View\LayoutInterface $layout
    ) {
        $this->profiler = $profiler;
        $this->profilerHelper = $profilerHelper;
        $this->layout = $layout;
    }

    public function execute(Observer $observer)
    {
        if ($this->isProfilerAction() || !$this->profilerHelper->isEnabled()) {
            return;
        }

        $request  = $observer->getRequest();
        $response = $observer->getResponse();

        $this->profiler->run($request, $response);
    }

    private function isProfilerAction()
    {
        return in_array('debug_profiler_info', $this->layout->getUpdate()->getHandles());
    }
}
