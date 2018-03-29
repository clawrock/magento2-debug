<?php

namespace ClawRock\Debug\Controller\Cache;

use ClawRock\Debug\Controller\Cache;
use Magento\Framework\Controller\ResultFactory;

class Enable extends Cache
{
    public function execute()
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $cacheType = $this->getRequest()->getParam('type');

        if (!$this->isValidCacheType($cacheType)) {
            $result->setHttpResponseCode(422);
            return $result;
        }

        $this->cacheState->setEnabled($cacheType, true);
        $this->cacheState->persist();

        return $result;
    }
}
