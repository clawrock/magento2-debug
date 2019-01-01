<?php

namespace ClawRock\Debug\Model\View\Renderer;

use Magento\Framework\View\Element\Template;

class TableRenderer implements RendererInterface
{
    const TEMPLATE = 'ClawRock_Debug::renderer/table.phtml';

    /**
     * @var array
     */
    private $items;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    /**
     * @var \ClawRock\Debug\Model\View\Renderer\VarRendererFactory
     */
    private $varRendererFactory;

    /**
     * @var array
     */
    private $labels;

    public function __construct(
        array $items,
        \Magento\Framework\View\LayoutInterface $layout,
        \ClawRock\Debug\Model\View\Renderer\VarRendererFactory $varRendererFactory,
        array $labels = []
    ) {
        $this->items = $items;
        $this->layout = $layout;
        $this->varRendererFactory = $varRendererFactory;
        $this->labels = $labels;
    }

    public function render(): string
    {
        return $this->layout->createBlock(Template::class)
            ->setTemplate(self::TEMPLATE)
            ->setData('items', $this->items)
            ->setData('labels', $this->labels)
            ->setData('var_renderer', $this->varRendererFactory)
            ->setData('key_label', $this->labels[0] ?? 'Key')
            ->setData('value_label', $this->labels[1] ?? 'Value')
            ->toHtml();
    }
}
