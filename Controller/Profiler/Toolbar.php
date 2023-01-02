<?php
declare(strict_types=1);

namespace ClawRock\Debug\Controller\Profiler;

use ClawRock\Debug\Model\Profiler;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;

class Toolbar implements HttpGetActionInterface
{
    private \ClawRock\Debug\Model\Storage\ProfileMemoryStorage $profileMemoryStorage;
    private \ClawRock\Debug\Api\ProfileRepositoryInterface $profileRepository;
    private \Magento\Framework\Controller\ResultFactory $resultFactory;
    private \Magento\Framework\App\RequestInterface $request;

    public function __construct(
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Framework\App\RequestInterface $request,
        \ClawRock\Debug\Model\Storage\ProfileMemoryStorage $profileMemoryStorage,
        \ClawRock\Debug\Api\ProfileRepositoryInterface $profileRepository
    ) {
        $this->resultFactory = $resultFactory;
        $this->request = $request;
        $this->profileMemoryStorage = $profileMemoryStorage;
        $this->profileRepository = $profileRepository;
    }

    public function execute(): ?\Magento\Framework\Controller\ResultInterface
    {
        $token = $this->request->getParam(Profiler::URL_TOKEN_PARAMETER);
        $profile = $this->profileRepository->getById($token);
        $this->profileMemoryStorage->write($profile);

        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
