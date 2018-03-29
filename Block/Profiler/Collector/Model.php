<?php

namespace ClawRock\Debug\Block\Profiler\Collector;

use ClawRock\Debug\Block\Profiler\Collector;

class Model extends Collector
{
    /**
     * @var \ClawRock\Debug\Block\Profiler\Renderer\CallStackRenderer
     */
    protected $callStackRenderer;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \ClawRock\Debug\Helper\Profiler $helper,
        \ClawRock\Debug\Block\Profiler\Renderer\TableRenderer $tableRenderer,
        \ClawRock\Debug\Block\Profiler\Renderer\CallStackRenderer $callStackRenderer,
        array $data = []
    ) {
        parent::__construct($context, $registry, $helper, $tableRenderer, $data);
        $this->callStackRenderer = $callStackRenderer;
    }

    public function renderCallStack($id, $stack)
    {
        return $this->callStackRenderer->render(['id' => $id, 'stack' => $stack]);
    }

    public function getOperations()
    {
        return ['save', 'delete', 'load'];
    }
}
