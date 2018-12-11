<?php

namespace ClawRock\Debug\Controller\Profiler;

use Magento\Framework\App\Action\Action;

class PHPInfo extends Action
{
    public function execute()
    {
        return phpinfo();
    }
}
