<?php
declare(strict_types=1);

namespace ClawRock\Debug\Controller\Cache;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

class Clean extends \ClawRock\Debug\Controller\Cache
{
    public function execute(): ?ResultInterface
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $types = $this->request->getParam('type');

        if (!$types) {
            $types = $this->cacheManager->getAvailableTypes();
        }

        $this->cacheManager->clean((array) $types);

        return $result;
    }
}
