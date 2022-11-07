<?php
declare(strict_types=1);

namespace ClawRock\Debug\Controller;

use Magento\Framework\App\Action\HttpGetActionInterface;

abstract class Cache implements HttpGetActionInterface
{
    protected \Magento\Framework\Controller\ResultFactory $resultFactory;
    protected \Magento\Framework\App\RequestInterface $request;
    protected \Magento\Framework\App\Cache\Manager $cacheManager;

    public function __construct(
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\Cache\Manager $cacheManager
    ) {
        $this->resultFactory = $resultFactory;
        $this->request = $request;
        $this->cacheManager = $cacheManager;
    }
}
