<?php

namespace ClawRock\Debug\Observer\Collector\LayoutDataCollector;

class BeforeToHtml implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \ClawRock\Debug\Model\DataCollector\LayoutDataCollector
     */
    private $dataCollector;

    public function __construct(
        \ClawRock\Debug\Model\DataCollector\LayoutDataCollector $dataCollector
    ) {
        $this->dataCollector = $dataCollector;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Framework\View\Element\AbstractBlock $block */
        $block = $observer->getBlock();
        $id = uniqid();
        $block->setData($this->dataCollector::BLOCK_PROFILER_ID_KEY, $id);

        $data = [
            'id'           => $id,
            'start_render' => microtime(true),
            'hash'         => spl_object_hash($block),
            'children'     => [],
            'parent_id'    => false,
        ];

        $parent = $block->getParentBlock();

        if ($parent) {
            $parentId = $parent->getData('profiler_id');
            $data['parent_id'] = $parentId;
            $this->dataCollector->addBlockChildren($parentId, $id);
        }

        $this->dataCollector->setBlockData($id, $data);
    }
}
