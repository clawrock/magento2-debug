<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\View\Renderer;

use Magento\Framework\View\Element\Template;

class QueryRenderer implements RendererInterface
{
    private const TEMPLATE = 'ClawRock_Debug::renderer/query.phtml';

    private \Zend_Db_Profiler_Query $query;
    private \Magento\Framework\View\LayoutInterface $layout;
    private \Magento\Framework\Math\Random $mathRandom;
    private \ClawRock\Debug\Model\View\Renderer\VarRenderer $varRenderer;
    private \ClawRock\Debug\Helper\Database $databaseHelper;

    public function __construct(
        \Zend_Db_Profiler_Query $query,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\Math\Random $mathRandom,
        \ClawRock\Debug\Model\View\Renderer\VarRenderer $varRenderer,
        \ClawRock\Debug\Helper\Database $databaseHelper
    ) {
        $this->query = $query;
        $this->layout = $layout;
        $this->mathRandom = $mathRandom;
        $this->varRenderer = $varRenderer;
        $this->databaseHelper = $databaseHelper;
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
                    'query' => $this->query,
                    'highlighted_query' => $this->databaseHelper->highlightQuery($this->query->getQuery()),
                    'formatted_query' => $this->databaseHelper->formatQuery($this->query->getQuery()),
                    'runnable_query' => $this->databaseHelper->highlightQuery(
                        $this->databaseHelper->replaceQueryParameters(
                            $this->query->getQuery(),
                            $this->query->getQueryParams()
                        )
                    ),
                    'var_renderer' => $this->varRenderer,
                    'uniq_id' => $this->mathRandom->getUniqueHash(),
                ],
            ]
        );

        return $block->toHtml();
    }
}
