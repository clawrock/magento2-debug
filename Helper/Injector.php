<?php

namespace ClawRock\Debug\Helper;

use Magento\Framework\HTTP\PhpEnvironment\Request;
use Magento\Framework\HTTP\PhpEnvironment\Response;
use Magento\Framework\View\Element\Template;

class Injector
{
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    /**
     * @var \ClawRock\Debug\Model\View\Toolbar
     */
    private $viewModel;

    public function __construct(
        \Magento\Framework\View\LayoutInterface $layout,
        \ClawRock\Debug\Model\View\Toolbar $viewModel
    ) {
        $this->layout = $layout;
        $this->viewModel = $viewModel;
    }

    public function inject(Request $request, Response $response, $token = null)
    {
        $content = $response->getBody();
        $pos = strripos($content, '</body>');

        if (false !== $pos) {
            /** @var Toolbar $toolbarBlock */
            $toolbarBlock = $this->layout->createBlock(Template::class, 'debug.toolbar');
            $toolbarBlock->setTemplate('ClawRock_Debug::profiler/toolbar/js.phtml')->setData([
                'view_model' => $this->viewModel,
                'token'     => $token,
                'request'   => $request,
            ]);

            /** @var Template $jsBlock */
            $jsBlock = $this->layout->createBlock(Template::class, 'debug.profiler.js');
            $jsBlock->setTemplate('ClawRock_Debug::profiler/js.phtml');

            $toolbarBlock->setChild('debug_profiler_js', $jsBlock);

            $toolbar = "\n" . str_replace("\n", '', $toolbarBlock->toHtml()) . "\n";
            $content = substr($content, 0, $pos) . $toolbar . substr($content, $pos);
            $response->setBody($content);
        }
    }
}
