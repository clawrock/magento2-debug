<?php
declare(strict_types=1);

namespace ClawRock\Debug\Controller\Profiler;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\FileSystemException;

class Purge implements HttpGetActionInterface
{
    public function __construct(
        private \Magento\Framework\Controller\ResultFactory $resultFactory,
        private \Magento\Framework\App\Response\RedirectInterface $redirect,
        private \ClawRock\Debug\Model\Storage\ProfileFileStorage $profileFileStorage,
        private \Psr\Log\LoggerInterface $logger
    ) {
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
