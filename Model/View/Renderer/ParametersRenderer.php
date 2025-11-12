<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\View\Renderer;

use Magento\Framework\View\Element\Template;

class ParametersRenderer implements RendererInterface
{
    private const TEMPLATE = 'ClawRock_Debug::renderer/parameters.phtml';

    public function __construct(
        private \Laminas\Stdlib\ParametersInterface $parameters,
        private \Magento\Framework\View\LayoutInterface $layout,
        private \ClawRock\Debug\Model\View\Renderer\VarRenderer $varRenderer
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
                    'parameters' => $this->parameters,
                    'var_renderer' => $this->varRenderer,
                ],
            ]
        );

        return $block->toHtml();
    }
}
