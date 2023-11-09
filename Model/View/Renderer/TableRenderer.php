<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\View\Renderer;

use Magento\Framework\View\Element\Template;

class TableRenderer implements RendererInterface
{
    private const TEMPLATE = 'ClawRock_Debug::renderer/table.phtml';

    private array $items;
    private \Magento\Framework\View\LayoutInterface $layout;
    private \ClawRock\Debug\Model\View\Renderer\VarRenderer $varRenderer;
    private array $labels;

    public function __construct(
        array $items,
        \Magento\Framework\View\LayoutInterface $layout,
        \ClawRock\Debug\Model\View\Renderer\VarRenderer $varRenderer,
        array $labels = []
    ) {
        $this->items = $items;
        $this->layout = $layout;
        $this->varRenderer = $varRenderer;
        $this->labels = $labels;
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
                    'items' => $this->items,
                    'labels' => $this->labels,
                    'var_renderer' => $this->varRenderer,
                    'key_label' => $this->labels[0] ?? 'Key',
                    'value_label' => $this->labels[1] ?? 'Value',
                ],
            ]
        );

        return $block->toHtml();
    }
}
