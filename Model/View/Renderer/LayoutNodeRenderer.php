<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\View\Renderer;

use Magento\Framework\View\Element\Template;

class LayoutNodeRenderer implements RendererInterface
{
    private const TEMPLATE = 'ClawRock_Debug::renderer/layout/node.phtml';

    public function __construct(
        private \ClawRock\Debug\Model\ValueObject\LayoutNode $node,
        private \Magento\Framework\View\LayoutInterface $layout,
        private \ClawRock\Debug\Model\View\Renderer\LayoutNodeRendererFactory $layoutNodeRendererFactory,
        private \ClawRock\Debug\Helper\Formatter $formatter
    ) {
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
                    'node' => $this->node,
                    'formatter' => $this->formatter,
                    'layout_node_renderer' => $this->layoutNodeRendererFactory,
                ],
            ]
        );

        return $block->toHtml();
    }
}
