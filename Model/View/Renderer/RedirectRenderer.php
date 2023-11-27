<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\View\Renderer;

use Magento\Framework\View\Element\Template;

class RedirectRenderer implements RendererInterface
{
    private const TEMPLATE = 'ClawRock_Debug::renderer/redirect.phtml';

    /**
     * @var \ClawRock\Debug\Model\ValueObject\Redirect
     */
    private $redirect;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    /**
     * @var \ClawRock\Debug\Helper\Url
     */
    private $url;

    public function __construct(
        \ClawRock\Debug\Model\ValueObject\Redirect $redirect,
        \Magento\Framework\View\LayoutInterface $layout,
        \ClawRock\Debug\Helper\Url $url
    ) {
        $this->redirect = $redirect;
        $this->layout = $layout;
        $this->url = $url;
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
                ],
            ]
        );

        return $block->setProfilerUrl($this->url->getProfilerUrl($this->redirect->getToken()))
            ->setRedirect($this->redirect)
            ->toHtml();
    }
}
