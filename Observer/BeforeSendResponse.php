<?php

namespace ClawRock\Debug\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class BeforeSendResponse implements ObserverInterface
{
    /**
     * @var \ClawRock\Debug\Model\Profiler
     */
    protected $profiler;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layout;

    public function __construct(
        \ClawRock\Debug\Model\Profiler $profiler,
        \Magento\Framework\View\LayoutInterface $layout
    ) {
        $this->profiler = $profiler;
        $this->layout = $layout;
    }

    public function execute(Observer $observer)
    {
        if ($this->isProfilerAction()) {
            return;
        }

        $request  = $observer->getRequest();
        $response = $observer->getResponse();

        $this->profiler->run($request, $response);
    }

    protected function isProfilerAction()
    {
        return in_array('debug_profiler_info', $this->layout->getUpdate()->getHandles());
    }
}
