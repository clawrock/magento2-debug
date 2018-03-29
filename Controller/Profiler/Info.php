<?php

namespace ClawRock\Debug\Controller\Profiler;

use ClawRock\Debug\Block\Profiler\Collector;
use ClawRock\Debug\Controller\Debug;
use ClawRock\Debug\Model\Profiler;
use Magento\Framework\Controller\ResultFactory;

class Info extends Debug
{
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $registry,
        \ClawRock\Debug\Model\Profiler $profiler,
        \Magento\Framework\View\LayoutInterface $layout
    ) {
        parent::__construct($context, $registry, $profiler);

        $this->layout = $layout;
    }

    public function execute()
    {
        $request = $this->getRequest();
        $token = $request->getParam(Profiler::URL_TOKEN_PARAMETER);

        if (!$token) {
            return $this->_redirect('_profiler');
        }

        $panel = $request->getParam('panel', 'request');
        if ('latest' === $token && $latest = current($this->profiler->find(null, null, 1, null, null, null))) {
            $token = $latest['token'];
        }

        if (!$profile = $this->profiler->loadProfile($token)) {
            return $this->_forward('index', 'noroute', 'cms');
        }

        if (!$profile->hasCollector($panel)) {
            throw new \Exception(sprintf('Panel "%s" is not available for token "%s".', $panel, $token));
        }

        /** @var \Magento\Framework\View\Result\Page $page */
        $page = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $this->registry->register('current_profile', $profile);
        $this->registry->register('current_panel', $panel);

        $collector = $profile->getCollector($panel);

        $page->addPageLayoutHandles([
            'panel' => $collector->getCollectorName(),
            'profiler' => 'info',
        ], 'debug');

        $panelBlock = $this->layout->getBlock('debug.profiler.panel.content');

        if (!$panelBlock) {
            throw new \Exception(sprintf('Panel Block for "%s" is not available for token "%s".', $panel, $token));
        }
        if (!($panelBlock instanceof Collector)) {
            throw new \Exception(sprintf('Panel Block must extend "' . Collector::class . '"'));
        }

        $panelBlock->setCollector($collector);

        return $page;
    }
}
