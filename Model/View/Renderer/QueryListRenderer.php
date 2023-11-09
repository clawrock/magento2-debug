<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\View\Renderer;

use Magento\Framework\View\Element\Template;

class QueryListRenderer implements RendererInterface
{
    private const TEMPLATE = 'ClawRock_Debug::renderer/query/list.phtml';

    /** @var \Zend_Db_Profiler_Query[] */
    private array $queries;
    private \Magento\Framework\View\LayoutInterface $layout;
    private \Magento\Framework\Math\Random $mathRandom;
    private \ClawRock\Debug\Model\View\Renderer\QueryRendererFactory $queryRendererFactory;
    private \ClawRock\Debug\Helper\Formatter $formatter;

    public function __construct(
        array $queries,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\Math\Random $mathRandom,
        \ClawRock\Debug\Model\View\Renderer\QueryRendererFactory $queryRendererFactory,
        \ClawRock\Debug\Helper\Formatter $formatter
    ) {
        $this->queries = $queries;
        $this->layout = $layout;
        $this->mathRandom = $mathRandom;
        $this->queryRendererFactory = $queryRendererFactory;
        $this->formatter = $formatter;
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
