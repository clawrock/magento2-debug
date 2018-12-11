<?php

namespace ClawRock\Debug\Model\View\Renderer;

use Magento\Framework\View\Element\Template;

class LayoutNodeRenderer implements RendererInterface
{
    const TEMPLATE = 'ClawRock_Debug::renderer/layout/node.phtml';

    /**
     * @var \ClawRock\Debug\Model\ValueObject\LayoutNode
     */
    private $node;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    /**
     * @var \ClawRock\Debug\Model\View\Renderer\LayoutNodeRendererFactory
     */
    private $layoutNodeRendererFactory;

    /**
     * @var \ClawRock\Debug\Helper\Formatter
     */
    private $formatter;

    public function __construct(
        \ClawRock\Debug\Model\ValueObject\LayoutNode $node,
        \Magento\Framework\View\LayoutInterface $layout,
        \ClawRock\Debug\Model\View\Renderer\LayoutNodeRendererFactory $layoutNodeRendererFactory,
        \ClawRock\Debug\Helper\Formatter $formatter
    ) {
        $this->node = $node;
        $this->layout = $layout;
        $this->layoutNodeRendererFactory = $layoutNodeRendererFactory;
        $this->formatter = $formatter;
    }

    public function render(): string
    {
        return $this->layout->createBlock(Template::class)
            ->setTemplate(self::TEMPLATE)
            ->setData([
                'node' => $this->node,
                'formatter' => $this->formatter,
                'layout_node_renderer' => $this->layoutNodeRendererFactory,
            ])
            ->toHtml();
    }
}
