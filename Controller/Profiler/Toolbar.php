<?php
declare(strict_types=1);

namespace ClawRock\Debug\Controller\Profiler;

use ClawRock\Debug\Model\Profiler;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;

class Toolbar implements HttpGetActionInterface
{
    public function __construct(
        private \Magento\Framework\Controller\ResultFactory $resultFactory,
        private \Magento\Framework\App\RequestInterface $request,
        private \ClawRock\Debug\Model\Storage\ProfileMemoryStorage $profileMemoryStorage,
        private \ClawRock\Debug\Api\ProfileRepositoryInterface $profileRepository
    ) {
    }

    public function execute(): ?\Magento\Framework\Controller\ResultInterface
    {
        $token = $this->request->getParam(Profiler::URL_TOKEN_PARAMETER);
        $profile = $this->profileRepository->getById($token);
        $this->profileMemoryStorage->write($profile);

        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
