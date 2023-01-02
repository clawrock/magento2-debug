<?php
declare(strict_types=1);

namespace ClawRock\Debug\Controller\Profiler;

use ClawRock\Debug\Model\Profiler;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;

class Info implements HttpGetActionInterface
{
    private \Magento\Framework\View\LayoutInterface $layout;
    private \ClawRock\Debug\Api\ProfileRepositoryInterface $profileRepository;
    private \ClawRock\Debug\Model\Storage\ProfileMemoryStorage $profileMemoryStorage;
    private \Magento\Framework\Controller\ResultFactory $resultFactory;
    private \Magento\Framework\App\RequestInterface $request;

    public function __construct(
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\View\LayoutInterface $layout,
        \ClawRock\Debug\Api\ProfileRepositoryInterface $profileRepository,
        \ClawRock\Debug\Model\Storage\ProfileMemoryStorage $profileMemoryStorage
    ) {
        $this->resultFactory = $resultFactory;
        $this->request = $request;
        $this->layout = $layout;
        $this->profileRepository = $profileRepository;
        $this->profileMemoryStorage = $profileMemoryStorage;
    }

    public function execute(): ?ResultInterface
    {
        $token = $this->request->getParam(Profiler::URL_TOKEN_PARAMETER, '');
        $profile = ($token === 'latest'
            ? $this->profileRepository->findLatest()
            : $this->profileRepository->getById($token));

        $panel = $this->request->getParam('panel', 'request');

        if (!$profile->hasCollector($panel)) {
            throw new LocalizedException(__('Panel "%s" is not available for token "%s".', $panel, $token));
        }

        /** @var \Magento\Framework\View\Result\Page $page */
        $page = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $this->profileMemoryStorage->write($profile);
        $collector = $profile->getCollector($panel);

        $page->addPageLayoutHandles([
            'panel' => $panel,
            'profiler' => 'info',
        ], 'debug');

        $panelBlock = $this->layout->getBlock('debug.profiler.panel.content');

        if (!$panelBlock instanceof \Magento\Framework\View\Element\AbstractBlock) {
            throw new LocalizedException(__('Panel Block for "%1" is not available for token "%2".', $panel, $token));
        }

        $panelBlock->setCollector($collector);

        return $page;
    }
}
