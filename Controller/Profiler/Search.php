<?php

namespace ClawRock\Debug\Controller\Profiler;

use ClawRock\Debug\Controller\Debug;
use ClawRock\Debug\Model\Profiler;
use Magento\Framework\Controller\ResultFactory;

class Search extends Debug
{
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layout;

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
        $request  = $this->getRequest();

        if (!empty($token = $request->getParam('_token'))) {
            return $this->_redirect('_debug/profiler/info', [Profiler::URL_TOKEN_PARAMETER => $token]);
        }

        if ($request->getParam('purge')) {
            $this->profiler->flush();
        }

        $ip = preg_replace('/[^:\d\.]/', '', $request->getParam('ip'));
        $method = $request->getParam('method');
        $statusCode = $request->getParam('status_code');
        $url = $request->getParam('url');
        $start = $request->getParam('start');
        $end = $request->getParam('end');
        $limit = $request->getParam('limit');

        $data = [
            'request' => $request,
            'tokens' => $this->profiler->find($ip, $url, $limit, $method, $start, $end),
            'ip' => $ip,
            'method' => $method,
            'status_code' => $statusCode,
            'url' => $url,
            'start' => $start,
            'end' => $end,
            'limit' => $limit,
            'panel' => null,
        ];

        /** @var \Magento\Framework\View\Result\Page $page */
        $page = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $page->addPageLayoutHandles([
            'profiler' => 'info',
        ], 'debug');

        $this->layout->getBlock('debug.profiler.panel.content')->setData($data);

        return $page;
    }
}
