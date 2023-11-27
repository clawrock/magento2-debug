<?php
declare(strict_types=1);

namespace ClawRock\Debug\Helper;

use Magento\Framework\HTTP\PhpEnvironment\Request;
use Magento\Framework\HTTP\PhpEnvironment\Response;
use Magento\Framework\View\Element\Template;

class Injector
{
    private const TEMPLATE_TOOLBAR = 'ClawRock_Debug::profiler/toolbar/js.phtml';
    private const TEMPLATE_BLOCK = 'ClawRock_Debug::profiler/js.phtml';

    private \Magento\Framework\View\LayoutInterface $layout;
    private \ClawRock\Debug\Model\View\Toolbar $viewModel;

    public function __construct(
        \Magento\Framework\View\LayoutInterface $layout,
        \ClawRock\Debug\Model\View\Toolbar $viewModel
    ) {
        $this->layout = $layout;
        $this->viewModel = $viewModel;
    }

    public function inject(Request $request, Response $response, ?string $token = null): void
    {
        $content = $response->getBody();
        $pos = strripos($content, '</body>');

        if (false !== $pos) {
            /** @var \Magento\Framework\View\Element\Template $toolbarBlock */
            $toolbarBlock = $this->layout->createBlock(
                Template::class,
                'debug.toolbar',
                [
                    'data' => [
                        'template'   => self::TEMPLATE_TOOLBAR,
                        'view_model' => $this->viewModel,
                        'token'      => $token,
                        'request'    => $request,
                    ],
                ]
            );

            /** @var \Magento\Framework\View\Element\Template $jsBlock */
            $jsBlock = $this->layout->createBlock(
                Template::class,
                'debug.profiler.js',
                [
                    'data' => [
                        'template' => self::TEMPLATE_BLOCK,
                    ],
                ]
            );

            $toolbarBlock->setChild('debug_profiler_js', $jsBlock);

            $toolbar = "\n" . str_replace("\n", '', $toolbarBlock->toHtml()) . "\n";
            $content = substr($content, 0, $pos) . $toolbar . substr($content, $pos);
            $response->setBody($content);
        }
    }
}
