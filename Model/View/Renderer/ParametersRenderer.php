<?php

namespace ClawRock\Debug\Model\View\Renderer;

use Magento\Framework\View\Element\Template;

class ParametersRenderer implements RendererInterface
{
    const TEMPLATE = 'ClawRock_Debug::renderer/parameters.phtml';

    /**
     * @var \Zend\Stdlib\ParametersInterface
     */
    private $parameters;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    /**
     * @var \ClawRock\Debug\Model\View\Renderer\VarRendererFactory
     */
    private $varRendererFactory;

    public function __construct(
        \Zend\Stdlib\ParametersInterface $parameters,
        \Magento\Framework\View\LayoutInterface $layout,
        \ClawRock\Debug\Model\View\Renderer\VarRendererFactory $varRendererFactory
    ) {
        $this->parameters = $parameters;
        $this->layout = $layout;
        $this->varRendererFactory = $varRendererFactory;
    }

    public function render(): string
    {
        return $this->layout->createBlock(Template::class)
            ->setTemplate(self::TEMPLATE)
            ->setData('parameters', $this->parameters)
            ->setData('var_renderer', $this->varRendererFactory)
            ->toHtml();
    }
}
