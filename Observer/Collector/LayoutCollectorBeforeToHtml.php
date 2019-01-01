<?php

namespace ClawRock\Debug\Observer\Collector;

use ClawRock\Debug\Model\Collector\LayoutCollector;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class LayoutCollectorBeforeToHtml implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        /** @var \Magento\Framework\View\Element\AbstractBlock $block */
        $block = $observer->getBlock();
        $id = uniqid();
        $block->addData([
            LayoutCollector::BLOCK_PROFILER_ID_KEY => $id,
            LayoutCollector::BLOCK_START_RENDER_KEY => microtime(true),
            LayoutCollector::BLOCK_HASH_KEY => spl_object_hash($block),
        ]);

        if ($block->getParentBlock()) {
            $block->addData([
                LayoutCollector::BLOCK_PARENT_PROFILER_ID_KEY => $block->getParentBlock()
                    ->getData(LayoutCollector::BLOCK_PROFILER_ID_KEY),
            ]);
        }
    }
}
