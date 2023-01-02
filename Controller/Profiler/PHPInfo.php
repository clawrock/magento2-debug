<?php
declare(strict_types=1);

namespace ClawRock\Debug\Controller\Profiler;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

class PHPInfo implements HttpGetActionInterface
{
    private \Magento\Framework\Controller\ResultFactory $resultFactory;

    public function __construct(
        \Magento\Framework\Controller\ResultFactory $resultFactory
    ) {
        $this->resultFactory = $resultFactory;
    }

    public function execute(): ?ResultInterface
    {
        phpinfo();

        return $this->resultFactory->create(ResultFactory::TYPE_RAW);
    }
}
