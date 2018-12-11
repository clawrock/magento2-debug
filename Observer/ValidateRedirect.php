<?php

namespace ClawRock\Debug\Observer;

use ClawRock\Debug\Model\Info\RequestInfo;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ValidateRedirect implements ObserverInterface
{
    /**
     * @var \ClawRock\Debug\Model\Session
     */
    private $session;

    public function __construct(
        \ClawRock\Debug\Model\Session\Proxy $session
    ) {
        $this->session = $session;
    }

    public function execute(Observer $observer)
    {
        if ($this->session->getData(RequestInfo::REDIRECT_PARAM)) {
            $observer->getRequest()->setParam('_redirected', true);
        }
    }
}
