<?php

namespace ClawRock\Debug\Controller\Profiler;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;

class PHPInfo extends Action
{
    public function execute()
    {
        phpinfo();

        return $this->resultFactory->create(ResultFactory::TYPE_RAW);
    }
}
