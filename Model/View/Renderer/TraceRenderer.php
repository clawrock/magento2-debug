<?php

namespace ClawRock\Debug\Model\View\Renderer;

use Magento\Framework\View\Element\Template;

class TraceRenderer implements RendererInterface
{
    const TEMPLATE = 'ClawRock_Debug::renderer/trace.phtml';

    /**
     * @var string
     */
    private $id;

    /**
     * @var array
     */
    private $trace;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    /**
     * @var \ClawRock\Debug\Model\View\Renderer\TraceCallRendererFactory
     */
    private $traceCallRendererFactory;

    public function __construct(
        array $trace,
        \Magento\Framework\View\LayoutInterface $layout,
        \ClawRock\Debug\Model\View\Renderer\TraceCallRendererFactory $traceCallRendererFactory
    ) {
        $this->id = uniqid();
        $this->trace = $trace;
        $this->layout = $layout;
        $this->traceCallRendererFactory = $traceCallRendererFactory;
    }

    public function render(): string
    {
        return $this->layout->createBlock(Template::class)
            ->setTemplate(self::TEMPLATE)
            ->setData('trace', $this->trace)
            ->setData('trace_id', $this->id)
            ->setData('trace_call_renderer', $this->traceCallRendererFactory)
            ->toHtml();
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}
