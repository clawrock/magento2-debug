<?php

namespace ClawRock\Debug\Controller\Profiler;

use ClawRock\Debug\Model\Profile\Criteria;
use ClawRock\Debug\Model\Profiler;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;

class Search extends Action
{
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    /**
     * @var \ClawRock\Debug\Api\ProfileRepositoryInterface
     */
    private $profileRepository;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\LayoutInterface $layout,
        \ClawRock\Debug\Api\ProfileRepositoryInterface $profileRepository
    ) {
        parent::__construct($context);

        $this->layout = $layout;
        $this->profileRepository = $profileRepository;
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $request  = $this->getRequest();

        if (!empty($token = $request->getParam('_token'))) {
            return $this->_redirect('_debug/profiler/info', [Profiler::URL_TOKEN_PARAMETER => $token]);
        }

        /** @var \Magento\Framework\View\Result\Page $page */
        $page = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $page->addPageLayoutHandles([
            'profiler' => 'info',
        ], 'debug');

        $criteria = Criteria::createFromRequest($request);

        $this->layout->getBlock('debug.profiler.panel.content')->addData([
            'results' => $this->profileRepository->find($criteria),
            'criteria' => $criteria,
        ]);

        return $page;
    }
}
