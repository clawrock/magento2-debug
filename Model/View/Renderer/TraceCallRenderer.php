<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\View\Renderer;

use Magento\Framework\View\Element\Template;

class TraceCallRenderer implements RendererInterface
{
    private const TEMPLATE = 'ClawRock_Debug::renderer/trace/call.phtml';
    private const CALL_INFO = ['function', 'class', 'line', 'file'];

    private array $call;
    private \Magento\Framework\View\LayoutInterface $layout;
    private \Magento\Framework\Filesystem\DirectoryList $directoryList;

    public function __construct(
        array $call,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\Filesystem\DirectoryList $directoryList
    ) {
        $this->call = $call;
        $this->layout = $layout;
        $this->directoryList = $directoryList;
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

        foreach (self::CALL_INFO as $info) {
            if (isset($this->call[$info])) {
                $block->setData($info, $this->call[$info]);
            }
        }
        if ($block->hasFile()) {
            $block->setFile($this->relativizePath($block->getFile()));
        }

        return $block->toHtml();
    }

    private function relativizePath(string $path): string
    {
        $rootDirectory = $this->directoryList->getRoot();

        return (string) (strpos($path, $rootDirectory) === 0 ? substr($path, strlen($rootDirectory)) : $path);
    }
}
