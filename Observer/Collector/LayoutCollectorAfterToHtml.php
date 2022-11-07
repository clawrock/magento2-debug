<?php
declare(strict_types=1);

namespace ClawRock\Debug\Observer\Collector;

use ClawRock\Debug\Model\Collector\LayoutCollector;
use ClawRock\Debug\Model\ValueObject\Block;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class LayoutCollectorAfterToHtml implements ObserverInterface
{
    private \ClawRock\Debug\Model\Collector\LayoutCollector $layoutCollector;

    public function __construct(
        \ClawRock\Debug\Model\Collector\LayoutCollector $layoutCollector
    ) {
        $this->layoutCollector = $layoutCollector;
    }

    public function execute(Observer $observer)
    {
        /** @var \Magento\Framework\View\Element\AbstractBlock $block */
        $block = $observer->getBlock();

        $renderedTimestamp = microtime(true);
        $renderTime = $renderedTimestamp - $block->getData(LayoutCollector::BLOCK_START_RENDER_KEY);

        $block->addData([
            LayoutCollector::BLOCK_STOP_RENDER_KEY => $renderedTimestamp,
            LayoutCollector::RENDER_TIME     => $renderTime,
        ]);

        $this->layoutCollector->log(new Block($block));
    }
}
