<?php

namespace ClawRock\Debug\Model\Info;

use ClawRock\Debug\Model\Collector\LayoutCollector;
use ClawRock\Debug\Model\ValueObject\Block;

class LayoutInfo
{
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    public function __construct(
        \Magento\Framework\View\LayoutInterface $layout
    ) {
        $this->layout = $layout;
    }

    public function getHandles(): array
    {
        return $this->layout->getUpdate()->getHandles();
    }

    public function getCreatedBlocks(): array
    {
        $blocks = [];
        foreach ($this->layout->getAllBlocks() as $block) {
            $blocks[] = new Block($block);
        }

        return $blocks;
    }

    public function getNotRenderedBlocks(): array
    {
        $blocks = [];
        foreach ($this->layout->getAllBlocks() as $block) {
            /** @var \Magento\Framework\View\Element\AbstractBlock $block */
            if (!$block->getData(LayoutCollector::BLOCK_PROFILER_ID_KEY)) {
                $blocks[] = new Block($block);
            }
        }

        return $blocks;
    }
}
