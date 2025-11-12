<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\View\Renderer;

use Magento\Framework\View\Element\Template;

class RedirectRenderer implements RendererInterface
{
    private const TEMPLATE = 'ClawRock_Debug::renderer/redirect.phtml';

    public function __construct(
        private \ClawRock\Debug\Model\ValueObject\Redirect $redirect,
        private \Magento\Framework\View\LayoutInterface $layout,
        private \ClawRock\Debug\Helper\Url $url
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
                ],
            ]
        );

        return $block->setProfilerUrl($this->url->getProfilerUrl($this->redirect->getToken()))
            ->setRedirect($this->redirect)
            ->toHtml();
    }
}
