<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\View\Renderer;

use Magento\Framework\View\Element\Template;

class TraceRenderer implements RendererInterface
{
    private const TEMPLATE = 'ClawRock_Debug::renderer/trace.phtml';

    private string $id;

    public function __construct(
        private array $trace,
        private \Magento\Framework\View\LayoutInterface $layout,
        private \ClawRock\Debug\Model\View\Renderer\TraceCallRendererFactory $traceCallRendererFactory
    ) {
        $this->id = uniqid();
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
