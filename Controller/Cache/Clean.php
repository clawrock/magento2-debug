<?php

namespace ClawRock\Debug\Controller\Cache;

use ClawRock\Debug\Controller\Cache;
use Magento\Framework\Controller\ResultFactory;

class Clean extends Cache
{
    const CLEAN_ALL_PARAM = 'all';

    public function execute()
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $cacheType = $this->getRequest()->getParam('type');

        if ($cacheType === self::CLEAN_ALL_PARAM) {
            $types = array_keys($this->cacheTypeList->getTypes());
            foreach ($types as $type) {
                $this->cacheTypeList->cleanType($type);
            }

            return $result;
        }

        if (!$this->isValidCacheType($cacheType)) {
            $result->setHttpResponseCode(422);

            return $result;
        }

        $this->cacheTypeList->cleanType($cacheType);

        return $result;
    }
}
