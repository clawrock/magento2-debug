<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\View\Renderer;

use Magento\Framework\View\Element\Template;

class QueryListRenderer implements RendererInterface
{
    private const TEMPLATE = 'ClawRock_Debug::renderer/query/list.phtml';

    public function __construct(
        private array $queries,
        private \Magento\Framework\View\LayoutInterface $layout,
        private \Magento\Framework\Math\Random $mathRandom,
        private \ClawRock\Debug\Model\View\Renderer\QueryRendererFactory $queryRendererFactory,
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
                    'queries' => $this->queries,
                    'query_renderer' => $this->queryRendererFactory,
                    'prefix' => $this->mathRandom->getUniqueHash(),
                    'formatter' => $this->formatter,
                ],
            ]
        );

        return $block->toHtml();
    }
}
