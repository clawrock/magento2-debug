<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\View\Renderer;

use Magento\Framework\View\Element\Template;

class ParametersRenderer implements RendererInterface
{
    private const TEMPLATE = 'ClawRock_Debug::renderer/parameters.phtml';

    private \Laminas\Stdlib\ParametersInterface $parameters;
    private \Magento\Framework\View\LayoutInterface $layout;
    private \ClawRock\Debug\Model\View\Renderer\VarRenderer $varRenderer;

    public function __construct(
        \Laminas\Stdlib\ParametersInterface $parameters,
        \Magento\Framework\View\LayoutInterface $layout,
        \ClawRock\Debug\Model\View\Renderer\VarRenderer $varRenderer
    ) {
        $this->parameters = $parameters;
        $this->layout = $layout;
        $this->varRenderer = $varRenderer;
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
