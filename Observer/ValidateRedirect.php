<?php

namespace ClawRock\Debug\Observer;

use ClawRock\Debug\Model\DataCollector\RequestDataCollector;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ValidateRedirect implements ObserverInterface
{
    /**
     * @var \ClawRock\Debug\Model\Session
     */
    private $session;
    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\Request
     */
    private $request;

    public function __construct(
        \ClawRock\Debug\Model\Session $session,
        \Magento\Framework\HTTP\PhpEnvironment\Request $request
    ) {
        $this->session = $session;
        $this->request = $request;
    }

    public function execute(Observer $observer)
    {
        if ($this->session->getData(RequestDataCollector::REDIRECT_PARAM)) {
            $this->request->setParam('_redirected', true);
        }
    }
}
