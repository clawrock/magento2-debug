<?php
declare(strict_types=1);

namespace ClawRock\Debug\Observer;

use ClawRock\Debug\Model\Info\RequestInfo;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ValidateRedirect implements ObserverInterface
{
    public function __construct(
        private \ClawRock\Debug\Model\Session $session
    ) {
    }

    public function execute(Observer $observer)
    {
        if ($this->session->getData(RequestInfo::REDIRECT_PARAM)) {
            $observer->getRequest()->setParam('_redirected', true);
        }
    }
}
