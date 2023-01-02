<?php
declare(strict_types=1);

namespace ClawRock\Debug\Controller\Profiler;

use ClawRock\Debug\Model\Profile\Criteria;
use ClawRock\Debug\Model\Profiler;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

class Search implements HttpGetActionInterface
{
    private \Magento\Framework\Controller\ResultFactory $resultFactory;
    private \Magento\Framework\App\RequestInterface $request;
    private \Magento\Framework\View\LayoutInterface $layout;
    private \ClawRock\Debug\Api\ProfileRepositoryInterface $profileRepository;

    public function __construct(
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\View\LayoutInterface $layout,
        \ClawRock\Debug\Api\ProfileRepositoryInterface $profileRepository
    ) {
        $this->resultFactory = $resultFactory;
        $this->request = $request;
        $this->layout = $layout;
        $this->profileRepository = $profileRepository;
    }

    public function execute(): ?ResultInterface
    {
        if (!empty($token = $this->request->getParam('_token'))) {
            /** @var \Magento\Framework\Controller\Result\Redirect $result */
            $result = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $result->setPath('_debug/profiler/info', [Profiler::URL_TOKEN_PARAMETER => $token]);

            return $result;
        }

        /** @var \Magento\Framework\View\Result\Page $page */
        $page = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $page->addPageLayoutHandles([
            'profiler' => 'info',
        ], 'debug');

        $criteria = Criteria::createFromRequest($this->request);

        $contentBlock = $this->layout->getBlock('debug.profiler.panel.content');

        if ($contentBlock instanceof \Magento\Framework\View\Element\AbstractBlock) {
            $contentBlock->addData([
                'results' => $this->profileRepository->find($criteria),
                'criteria' => $criteria,
            ]);
        }

        return $page;
    }
}
