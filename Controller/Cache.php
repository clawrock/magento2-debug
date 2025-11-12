<?php
declare(strict_types=1);

namespace ClawRock\Debug\Controller;

use Magento\Framework\App\Action\HttpGetActionInterface;

abstract class Cache implements HttpGetActionInterface
{
    public function __construct(
        protected \Magento\Framework\Controller\ResultFactory $resultFactory,
        protected \Magento\Framework\App\RequestInterface $request,
        protected \Magento\Framework\App\Cache\Manager $cacheManager
    ) {
    }
}
