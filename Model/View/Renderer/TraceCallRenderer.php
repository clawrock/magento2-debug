<?php

namespace ClawRock\Debug\Model\View\Renderer;

use Magento\Framework\View\Element\Template;

class TraceCallRenderer implements RendererInterface
{
    const TEMPLATE = 'ClawRock_Debug::renderer/trace/call.phtml';

    const CALL_INFO = ['function', 'class', 'line', 'file'];

    /**
     * @var array
     */
    private $call;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    private $directoryList;

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
        $block = $this->layout->createBlock(Template::class);

        foreach (self::CALL_INFO as $info) {
            if (isset($this->call[$info])) {
                $block->setData($info, $this->call[$info]);
            }
        }
        if ($block->hasFile()) {
            $block->setFile($this->relativizePath($block->getFile()));
        }

        return $block->setTemplate(self::TEMPLATE)->toHtml();
    }

    private function relativizePath(string $path): string
    {
        $rootDirectory = $this->directoryList->getRoot();

        return (string) strpos($path, $rootDirectory) === 0 ? substr($path, strlen($rootDirectory)) : $path;
    }
}
