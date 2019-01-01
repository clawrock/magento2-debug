<?php

namespace ClawRock\Debug\Controller\Cache;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;

class Clean extends Action
{
    /**
     * @var \Magento\Framework\App\Cache\Manager
     */
    private $cacheManager;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Cache\Manager $cacheManager
    ) {
        parent::__construct($context);
        $this->cacheManager = $cacheManager;
    }

    public function execute()
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $types = $this->getRequest()->getParam('type');

        if (!$types) {
            $types = $this->cacheManager->getAvailableTypes();
        }

        $this->cacheManager->clean((array) $types);

        return $result;
    }
}
