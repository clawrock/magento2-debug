<?php

namespace ClawRock\Debug\Controller\Profiler;

use ClawRock\Debug\Controller\Debug;
use ClawRock\Debug\Model\Profiler;
use Magento\Framework\Controller\ResultFactory;

class Toolbar extends Debug
{
    public function execute()
    {
        $token = $this->getRequest()->getParam(Profiler::URL_TOKEN_PARAMETER);
        $profile = $this->profiler->loadProfile($token);
        $this->registry->register('current_profile', $profile);

        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
