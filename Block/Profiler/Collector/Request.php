<?php

namespace ClawRock\Debug\Block\Profiler\Collector;

use ClawRock\Debug\Block\Profiler\Collector;
use ClawRock\Debug\Block\Profiler\Renderer\DefaultRenderer;

class Request extends Collector
{
    /**
     * @var \ClawRock\Debug\Block\Profiler\Renderer\ParametersRenderer
     */
    protected $renderer;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \ClawRock\Debug\Helper\Profiler $helper,
        \ClawRock\Debug\Block\Profiler\Renderer\TableRenderer $tableRenderer,
        \ClawRock\Debug\Block\Profiler\Renderer\ParametersRenderer $renderer,
        array $data = []
    ) {
        parent::__construct($context, $registry, $helper, $tableRenderer, $data);
        $this->renderer = $renderer;
    }

    public function renderHandler($controller, $route = false, $method = false)
    {
        /** @var DefaultRenderer $renderer */
        $renderer = $this->_layout->createBlock(DefaultRenderer::class);
        $renderer->setTemplate('collector/toolbar/request/handler.phtml');
        return $renderer->render([
            'controller' => $controller,
            'route'      => $route,
            'method'     => $method,
        ]);
    }

    public function renderParameters($parameters, array $data = [])
    {
        $data['parameters'] = $parameters;

        return $this->renderer->render($data);
    }
}
