<?php
declare(strict_types=1);

namespace ClawRock\Debug\Controller\Cache;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

class Disable extends \ClawRock\Debug\Controller\Cache
{
    public function execute(): ?ResultInterface
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $types = $this->request->getParam('type');

        $this->cacheManager->setEnabled((array) $types, false);

        return $result;
    }
}
