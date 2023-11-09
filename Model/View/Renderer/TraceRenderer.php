<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\View\Renderer;

use Magento\Framework\View\Element\Template;

class TraceRenderer implements RendererInterface
{
    private const TEMPLATE = 'ClawRock_Debug::renderer/trace.phtml';

    private string $id;
    private array $trace;
    private \Magento\Framework\View\LayoutInterface $layout;
    private \ClawRock\Debug\Model\View\Renderer\TraceCallRendererFactory $traceCallRendererFactory;

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
        /** @var \Magento\Framework\View\Element\Template $block */
        $block = $this->layout->createBlock(
            Template::class,
            '',
            [
                'data' => [
                    'template' => self::TEMPLATE,
                    'trace' => $this->trace,
                    'trace_id' => $this->id,
                    'trace_call_renderer' => $this->traceCallRendererFactory,
                ],
            ]
        );

        return $block->toHtml();
    }

    public function getId(): string
    {
        return $this->id;
    }
}
