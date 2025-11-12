<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\View\Renderer;

use ClawRock\Debug\Model\ValueObject\Block;
use Magento\Framework\View\Element\Template;

class LayoutGraphRenderer implements RendererInterface
{
    private const TEMPLATE = 'ClawRock_Debug::renderer/layout/graph.phtml';

    private float $totalRenderTime;

    public function __construct(
        private array $blocks,
        string $totalRenderTime,
        private \Magento\Framework\View\LayoutInterface $layout,
        private \ClawRock\Debug\Model\ValueObject\LayoutNodeFactory $layoutNodeFactory,
        private \ClawRock\Debug\Model\View\Renderer\LayoutNodeRendererFactory $layoutNodeRendererFactory,
        \ClawRock\Debug\Helper\Formatter $formatter
    ) {
        $this->totalRenderTime = $formatter->revertMicrotime($totalRenderTime);
    }

    public function render(): string
    {
        /** @var \Magento\Framework\View\Element\Template $block */
        $block = $this->layout->createBlock(
            Template::class,
            'clawrock_layout_graph_renderer',
            [
                'data' => [
                    'template' => self::TEMPLATE,
                    'nodes' => $this->createNodes(),
                    'layout_node_renderer' => $this->layoutNodeRendererFactory,
                ],
            ]
        );

        return $block->toHtml();
    }

    private function createNodes(): array
    {
        $nodes = [];

        foreach ($this->blocks as $block) {
            if (!$block->getParentId()) {
                $children = $this->resolveChildren($block);
                $nodes[] = $this->layoutNodeFactory->create([
                    'block' => $block,
                    'layoutRenderTime' => $this->totalRenderTime,
                    'children' => $children,
                ]);
            }
        }

        return $nodes;
    }

    private function resolveChildren(Block $block, string $prefix = '', bool $sibling = false): array
    {
        $children = [];
        $childrenCount = count($block->getChildren());
        $i = 1;
        $prefix .= $sibling ? '│&nbsp;' : '&nbsp;';
        foreach ($block->getChildren() as $childId) {
            $child = array_filter($this->blocks, function ($block) use ($childId) {
                /** @var \ClawRock\Debug\Model\ValueObject\Block $block */
                return $block->getName() === $childId;
            });
            if (($child = array_shift($child)) === null) {
                continue;
            }
            $childChildren = $this->resolveChildren($child, $prefix, $i++ !== $childrenCount);
            $children[$childId] = $this->layoutNodeFactory->create([
                'block' => $child,
                'layoutRenderTime' => $this->totalRenderTime,
                'prefix' => $prefix,
                'children' => $childChildren,
            ]);
        }
        return $children;
    }
}
