<?php
declare(strict_types=1);

namespace ClawRock\Debug\Controller\Profiler;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\FileSystemException;

class Purge implements HttpGetActionInterface
{
    private \ClawRock\Debug\Model\Storage\ProfileFileStorage $profileFileStorage;
    private \ClawRock\Debug\Logger\Logger $logger;
    private \Magento\Framework\Controller\ResultFactory $resultFactory;
    private \Magento\Framework\App\Response\RedirectInterface $redirect;

    public function __construct(
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \ClawRock\Debug\Model\Storage\ProfileFileStorage $profileFileStorage,
        \ClawRock\Debug\Logger\Logger $logger
    ) {
        $this->resultFactory = $resultFactory;
        $this->redirect = $redirect;
        $this->profileFileStorage = $profileFileStorage;
        $this->logger = $logger;
    }

    public function execute(): ?ResultInterface
    {
        try {
            $this->profileFileStorage->purge();
        } catch (FileSystemException $e) {
            $this->logger->error('ClawRock_Debug: failed to purge file storage', ['exception' => $e]);
        }

        /** @var \Magento\Framework\Controller\Result\Redirect $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $result->setUrl($this->redirect->getRefererUrl());

        return $result;
    }
}
